<?php

namespace App\Services;

use App\Models\User;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\Checkout;
use Stripe\StripeClient;

class StripeService
{
    private StripeClient $stripeClient;

    public function __construct()
    {
        $this->stripeClient = new StripeClient(config('cashier.secret'));
    }

    /**
     * Retrieve Checkout Session object
     *
     * @param string $sessionId
     * @return array{payment_status:string, metadata:array}
     */
    public function retrieveCheckoutSession(string $sessionId): array
    {
        $session = Cashier::stripe()->checkout->sessions->retrieve($sessionId);

        return [
            'payment_status' => $session->payment_status,
            'metadata' => $session['metadata'] ?? [],
        ];
    }

    /**
     * Checkout
     *
     * @param User $user
     * @param int[] $priceIds
     * @param array $metadata
     * @return Checkout
     */
    public function checkout(User $user, array $priceIds, array $metadata = []): Checkout
    {
        $checkout = $user->checkout($priceIds, [
            'success_url' => route('checkout-success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('review'),
            'metadata' => $metadata,
            'payment_intent_data' => [
                'metadata' => $metadata,
            ],
        ]);

        return $checkout;
    }

    /**
     * Create product
     *
     * @param string $name
     * @param string $description
     * @param integer $price
     * @return array
     */
    public function createProduct(string $name, string $description, int $price): array
    {
        $product = $this->stripeClient->products->create([
            'name' => $name,
            'description' => $description,
        ]);

        $price = $this->stripeClient->prices->create([
            'currency' => 'usd',
            'unit_amount' => $price,
            'product' => $product->id,
        ]);

        return [$product, $price];
    }
}
