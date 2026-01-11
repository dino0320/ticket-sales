<?php

namespace App\Listeners;

use App\Consts\CheckoutConst;
use App\Jobs\CancelOrder;
use App\Repositories\TicketRepository;
use App\Repositories\UserOrderRepository;
use App\Repositories\UserTicketRepository;
use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\Stripe\StripeService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Events\WebhookReceived;
use RuntimeException;
use Throwable;

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
            try {
                DB::transaction(function () use ($event) {
                    $userOrderRepository = new UserOrderRepository();
                    $userTicketRepository = new UserTicketRepository();
                    $ticketRepository = new TicketRepository();
            
                    $metadata = StripeService::getMetadataFromEvent($event);
                    $userOrderId = $metadata['user_order_id'] ?? null;
                    $amount = StripeService::getAmountFromEvent($event);
                    if ($userOrderId === null || $amount === null) {
                        throw new RuntimeException('Failed to get user_order_id or amount.');
                    }
        
                    $userOrder = $userOrderRepository->selectById($userOrderId);
                    if ($userOrder === null || $userOrder->status !== CheckoutConst::ORDER_STATUS_PENDING) {
                        throw new RuntimeException("Failed to get a pending user order. user_order_id:{$userOrder->id}");
                    }

                    $tickets = $ticketRepository->selectByIdsForUpdate(array_column($userOrder->order_items, 'ticket_id'));
                    $numbersOfTickets = CheckoutService::getNumbersOfTickets($userOrder);
                    CheckoutService::decreaseNumbersOfTickets($tickets, $numbersOfTickets);
                    CheckoutService::decreaseNumbersOfReservedTickets($tickets, $numbersOfTickets);

                    CartService::deleteCart($userOrder->user_id);

                    $userOrder->amount = $amount;
                    $userOrder->status = CheckoutConst::ORDER_STATUS_COMPLETED;
 
                    $userOrderRepository->save($userOrder);
                    $userTicketRepository->upsert(CheckoutService::createUserTickets($userOrder));
                    $ticketRepository->upsert($tickets);
                });
            } catch (Throwable $e) {
                Log::error($e);
            }
        }

        if ($event->payload['type'] === 'checkout.session.expired') {
            $metadata = StripeService::getMetadataFromEvent($event);
            $userOrderId = $metadata['user_order_id'] ?? null;
            if ($userOrderId === null) {
                Log::error('Failed to get user_order_id.');
                return;
            }

            CancelOrder::dispatch($userOrderId);
        }
    }
}
