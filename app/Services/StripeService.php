<?php

namespace App\Services;

use Stripe\StripeClient;

class StripeService
{
    /**
     * Get StripeClient
     *
     * @return StripeClient
     */
    private static function getStripeClient(): StripeClient
    {
        return new StripeClient(config('cashier.secret'));
    }

    /**
     * Create product
     *
     * @param string $name
     * @param string $description
     * @param integer $price
     * @return array
     */
    public static function createProduct(string $name, string $description, int $price): array
    {
        $stripe = self::getStripeClient();

        $product = $stripe->products->create([
            'name' => $name,
            'description' => $description,
        ]);

        $price = $stripe->prices->create([
            'currency' => 'usd',
            'unit_amount' => $price,
            'product' => $product->id,
        ]);

        return [$product, $price];
    }
}
