<?php

namespace Tests\Unit;

use App\Support\Money;
use Tests\TestCase;

class MoneyTest extends TestCase
{
    public function test_formats_cents_in_the_given_currency(): void
    {
        $this->assertSame('€4,500.00', Money::format(450000, 'EUR'));
        $this->assertSame('$12.50', Money::format(1250, 'USD'));
        $this->assertSame('£0.00', Money::format(0, 'GBP'));
    }
}
