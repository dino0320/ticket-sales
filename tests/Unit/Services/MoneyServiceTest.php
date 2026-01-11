<?php

namespace Tests\Unit\Services;

use App\Services\MoneyService;
use PHPUnit\Framework\TestCase;

class MoneyServiceTest extends TestCase
{
    /**
     * Test convertDollarsToCents()
     */
    public function test_convert_dollars_to_cents(): void
    {
        $this->assertSame(12345, MoneyService::convertDollarsToCents(123.4567));
    }

    /**
     * Test convertCentsToDollars()
     */
    public function test_convert_cents_to_dollars(): void
    {
        $this->assertSame(12345.67, MoneyService::convertCentsToDollars(1234567));
    }
}
