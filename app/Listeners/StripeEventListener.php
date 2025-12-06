<?php

namespace App\Listeners;

use App\Consts\CheckoutConst;
use App\Repositories\UserOrderRepository;
use App\Repositories\UserTicketRepository;
use App\Services\CartService;
use App\Services\CheckoutService;
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
            $userOrderRepository = new UserOrderRepository();
            $userTicketRepository = new UserTicketRepository();
            
            $userOrderId = $event->payload['data']['object']['metadata']['user_order_id'] ?? null;
            $amount = $event->payload['data']['object']['amount'] ?? null;
            if ($userOrderId === null || $amount === null) {
                return;
            }
        
            $userOrder = $userOrderRepository->selectById($userOrderId);
            if ($userOrder === null || $userOrder->status === CheckoutConst::ORDER_STATUS_COMPLETED) {
                return;
            }

            CartService::deleteAllUserCarts($userOrder->user_id);

            $userOrder->amount = $amount;
            $userOrder->status = CheckoutConst::ORDER_STATUS_COMPLETED;
 
            $userOrderRepository->save($userOrder);
            $userTicketRepository->upsert(CheckoutService::createUserTickets($userOrder));
        }
    }
}
