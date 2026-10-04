<?php

namespace App\Support;

/**
 * Hex colour checks and WCAG contrast, for colours that come from data
 * (store accents, custom button colours) rather than the design tokens.
 */
class Color
{
    public const string INK_BLACK = '#1d1d1b';

    public const string PARCHMENT = '#e2dedb';

    /**
     * A plain #rrggbb value, safe to output into a style attribute.
     */
    public static function isHex(?string $value): bool
    {
        return $value !== null && preg_match('/^#[0-9a-fA-F]{6}$/', $value) === 1;
    }

    /**
     * WCAG contrast ratio between two #rrggbb colours, from 1 to 21.
     */
    public static function contrast(string $a, string $b): float
    {
        [$lighter, $darker] = [max(self::luminance($a), self::luminance($b)), min(self::luminance($a), self::luminance($b))];

        return ($lighter + 0.05) / ($darker + 0.05);
    }

    /**
     * Ink-black or parchment, whichever reads better on the background.
     */
    public static function readableOn(string $background): string
    {
        return self::contrast($background, self::INK_BLACK) >= self::contrast($background, self::PARCHMENT)
            ? self::INK_BLACK
            : self::PARCHMENT;
    }

    protected static function luminance(string $hex): float
    {
        $channels = array_map(function (string $pair): float {
            $value = hexdec($pair) / 255;

            return $value <= 0.03928 ? $value / 12.92 : (($value + 0.055) / 1.055) ** 2.4;
        }, str_split(substr($hex, 1), 2));

        return 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2];
    }
}
