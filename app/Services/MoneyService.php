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
        return $dollars * 100;
    }

    /**
     * Convert cents to dollars
     *
     * @param float $cents
     * @return float
     */
    public static function convertCentsToDollars(float $cents): float
    {
        return $cents / 100;
    }
}
