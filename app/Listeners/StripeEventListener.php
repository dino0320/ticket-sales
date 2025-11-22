<?php

namespace App\Listeners;

use App\Consts\CheckoutConst;
use App\Repositories\UserCartRepository;
use App\Repositories\UserOrderRepository;
use App\Repositories\UserTicketRepository;
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
            $userCartRepository = new UserCartRepository();
            $userOrderRepository = new UserOrderRepository();
            $userTicketRepository = new UserTicketRepository();
            
            $userOrderId = $event->payload['data']['object']['metadata']['user_order_id'] ?? null;
            if ($userOrderId === null) {
                return;
            }
        
            $userOrder = $userOrderRepository->selectById($userOrderId);
            if ($userOrder === null || $userOrder->status === CheckoutConst::ORDER_STATUS_COMPLETED) {
                return;
            }

            $userCarts = $userCartRepository->selectByUserId($userOrder->user_id);

            $userOrder->status = CheckoutConst::ORDER_STATUS_COMPLETED;
 
            $userCartRepository->deleteMultiple($userCarts);
            $userOrderRepository->save($userOrder);
            $userTicketRepository->upsert(CheckoutService::createUserTickets($userOrder));
        }
    }
}
