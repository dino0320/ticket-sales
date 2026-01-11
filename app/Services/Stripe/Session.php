<?php

namespace App\Services\Stripe;

use OutOfBoundsException;
use RuntimeException;
use Stripe\Checkout\Session as CheckoutSession;

class Session
{
    /**
     * Payment status paid
     */
    public const PAYMENT_STATUS_PAID = 1;
    
    /**
     * Payment status unpaid
     */
    public const PAYMENT_STATUS_UNPAID = 2;

    /**
     * Payment status no payment required
     */
    public const PAYMENT_STATUS_NO_PAYMENT_REQUIRED = 3;

    /**
     * Property names
     */
    private const PROPERTIES = [
        'paymentStatus',
        'metadata',
    ];

    /**
     * Payment status
     *
     * @var integer
     */
    private int $paymentStatus;

    /**
     * Metadata
     *
     * @var array
     */
    private array $metadata;

    /**
     * @param string $paymentStatus Stripe Checkout Session's payment status
     * @param array $metadata Stripe Checkout Session's metadata
     */
    public function __construct(string $paymentStatus, array $metadata = [])
    {
        switch ($paymentStatus) {
            case CheckoutSession::PAYMENT_STATUS_PAID:
                $this->paymentStatus = self::PAYMENT_STATUS_PAID;
                break;
            
            case CheckoutSession::PAYMENT_STATUS_UNPAID:
                $this->paymentStatus = self::PAYMENT_STATUS_UNPAID;
                break;

            case CheckoutSession::PAYMENT_STATUS_NO_PAYMENT_REQUIRED:
                $this->paymentStatus = self::PAYMENT_STATUS_NO_PAYMENT_REQUIRED;
                break;

            default:
                throw new RuntimeException("Invalid payment status. payment_status:{$paymentStatus}");
        }

        $this->metadata = $metadata;
    }

    public function __get(string $key)
    {
        if (!in_array($key, self::PROPERTIES)) {
            throw new OutOfBoundsException("Invalid property. property:{$key}");
        }

        return $this->{$key};
    }
}