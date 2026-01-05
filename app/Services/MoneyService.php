<?php

namespace App\Services;

class MoneyService
{
    /**
     * Convert dollars to cents
     *
     * @param float $dollars
     * @return integer
     */
    public static function convertDollarsToCents(float $dollars): int
    {
        return (int)($dollars * 100);
    }

    /**
     * Convert cents to dollars
     *
     * @param integer $cents
     * @return float
     */
    public static function convertCentsToDollars(int $cents): float
    {
        return $cents / 100;
    }
}
