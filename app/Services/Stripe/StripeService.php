<?php

namespace App\Services\Stripe;

use App\Models\User;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\Checkout;
use Laravel\Cashier\Events\WebhookReceived;
use Stripe\StripeClient;

class StripeService
{
    private StripeClient $stripeClient;

    /**
     * Get metadata from webhook received event
     *
     * @param WebhookReceived $event
     * @return array
     */
    public static function getMetadataFromEvent(WebhookReceived $event): array
    {
        return $event->payload['data']['object']['metadata'] ?? [];
    }

    /**
     * Get amount from webhook received event
     *
     * @param WebhookReceived $event
     * @return integer|null
     */
    public static function getAmountFromEvent(WebhookReceived $event): ?int
    {
        return $event->payload['data']['object']['amount'] ?? null;
    }

    public function __construct()
    {
        $this->stripeClient = new StripeClient(config('cashier.secret'));
    }

    /**
     * Create Stripe Session
     *
     * @param string $sessionId
     * @return Session
     */
    public function createStripeSession(string $sessionId): Session
    {
        $session = Cashier::stripe()->checkout->sessions->retrieve($sessionId);
        return new Session($session['payment_status'], $session['metadata'] ?? []);
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
