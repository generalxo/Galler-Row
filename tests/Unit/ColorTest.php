<?php

namespace Tests\Unit;

use App\Support\Color;
use PHPUnit\Framework\TestCase;

class ColorTest extends TestCase
{
    public function test_is_hex_accepts_only_six_digit_hex(): void
    {
        $this->assertTrue(Color::isHex('#1f4e6e'));
        $this->assertTrue(Color::isHex('#ABCDEF'));
        $this->assertFalse(Color::isHex('#fff'));
        $this->assertFalse(Color::isHex('red'));
        $this->assertFalse(Color::isHex('#1f4e6e; background: url(x)'));
        $this->assertFalse(Color::isHex(null));
    }

    public function test_contrast_matches_wcag(): void
    {
        $this->assertEqualsWithDelta(21.0, Color::contrast('#000000', '#ffffff'), 0.01);
        $this->assertEqualsWithDelta(12.63, Color::contrast(Color::INK_BLACK, Color::PARCHMENT), 0.01);
        $this->assertEqualsWithDelta(1.0, Color::contrast('#1f4e6e', '#1f4e6e'), 0.01);
    }

    public function test_readable_on_picks_the_better_text_colour(): void
    {
        $this->assertSame(Color::PARCHMENT, Color::readableOn('#1f4e6e'));
        $this->assertSame(Color::INK_BLACK, Color::readableOn('#f2c14e'));
    }
}
