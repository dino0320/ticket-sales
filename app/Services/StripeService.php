<?php

namespace App\Services;

use App\Jobs\CancelOrder;
use App\Models\User;
use App\Models\UserOrder;
use Laravel\Cashier\Checkout;
use Stripe\StripeClient;
use Throwable;

class StripeService
{
    /**
     * Checkout
     *
     * @param User $user
     * @param UserOrder $userOrder
     * @return Checkout
     */
    public static function checkout(User $user, UserOrder $userOrder): Checkout
    {
        try {
            $checkout = $user->checkout(CheckoutService::getStripePriceIds($userOrder), [
                'success_url' => route('checkout-success').'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('review'),
                'metadata' => ['user_order_id' => $userOrder->id],
                'payment_intent_data' => [
                    'metadata' => ['user_order_id' => $userOrder->id],
                ],
            ]);

            return $checkout;
        } catch (Throwable $e) {
            CancelOrder::dispatch($userOrder->id);
            throw $e;
        }
    }

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
