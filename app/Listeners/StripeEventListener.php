<?php

namespace App\Listeners;

use App\Consts\CheckoutConst;
use App\Repositories\UserCartRepository;
use App\Repositories\UserOrderRepository;
use Laravel\Cashier\Events\WebhookReceived;

class StripeEventListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(WebhookReceived $event): void
    {
        if ($event->payload['type'] === 'payment_intent.succeeded') {
            $userCartRepository = new UserCartRepository();
            $userOrderRepository = new UserOrderRepository();
            
            $userOrderId = $event->payload['data']['object']['metadata']['user_order_id'] ?? null;
            if ($userOrderId === null) {
                return;
            }
        
            $userOrder = $userOrderRepository->selectById($userOrderId);
            if ($userOrder === null || $userOrder->status === CheckoutConst::ORDER_STATUS_COMPLETED) {
                return;
            }

            $userOrder->status = CheckoutConst::ORDER_STATUS_COMPLETED;

            $userCarts = $userCartRepository->selectByUserId($userOrder->user_id);
 
            $userCartRepository->deleteMultiple($userCarts);
            $userOrderRepository->save($userOrder);
        }
    }
}
