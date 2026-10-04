<?php

namespace App\Support;

use Illuminate\Support\Number;

/**
 * Formats whole-cent amounts for display.
 */
class Money
{
    public static function format(int $cents, string $currency): string
    {
        return (string) Number::currency($cents / 100, in: $currency, locale: app()->getLocale());
    }
}
