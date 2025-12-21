<?php

namespace App\Listeners;

use App\Consts\CheckoutConst;
use App\Jobs\CancelOrder;
use App\Repositories\TicketRepository;
use App\Repositories\UserOrderRepository;
use App\Repositories\UserTicketRepository;
use App\Services\CartService;
use App\Services\CheckoutService;
use Illuminate\Support\Facades\DB;
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
            DB::transaction(function () use ($event) {
                $userOrderRepository = new UserOrderRepository();
                $userTicketRepository = new UserTicketRepository();
                $ticketRepository = new TicketRepository();
            
                $userOrderId = $event->payload['data']['object']['metadata']['user_order_id'] ?? null;
                $amount = $event->payload['data']['object']['amount'] ?? null;
                if ($userOrderId === null || $amount === null) {
                    return;
                }
        
                $userOrder = $userOrderRepository->selectById($userOrderId);
                if ($userOrder === null || $userOrder->status !== CheckoutConst::ORDER_STATUS_PENDING) {
                    return;
                }

                $tickets = $ticketRepository->selectByIdsForUpdate(array_column($userOrder->order_items, 'ticket_id'));
                $numbersOfTickets = CheckoutService::getNumbersOfTickets($userOrder);
                CheckoutService::decreaseNumbersOfTickets($tickets, $numbersOfTickets);
                CheckoutService::decreaseNumbersOfReservedTickets($tickets, $numbersOfTickets);

                CartService::deleteAllUserCarts($userOrder->user_id);

                $userOrder->amount = $amount;
                $userOrder->status = CheckoutConst::ORDER_STATUS_COMPLETED;
 
                $userOrderRepository->save($userOrder);
                $userTicketRepository->upsert(CheckoutService::createUserTickets($userOrder));
                $ticketRepository->upsert($tickets);
            });
        }

        if ($event->payload['type'] === 'checkout.session.expired') {
            $userOrderId = $event->payload['data']['object']['metadata']['user_order_id'] ?? null;
            if ($userOrderId === null) {
                return;
            }

            CancelOrder::dispatch($userOrderId);
        }
    }
}
