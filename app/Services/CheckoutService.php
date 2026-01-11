<?php

namespace App\Services;

use App\Jobs\CancelOrder;
use App\Models\Ticket;
use App\Models\User;
use App\Models\UserOrder;
use App\Models\UserTicket;
use App\Services\Stripe\StripeService;
use Laravel\Cashier\Checkout;
use RuntimeException;
use Throwable;

class CheckoutService
{
    /**
     * Get stripe price ids
     *
     * @param UserOrder $userOrder
     * @return int[]
     */
    public static function getStripePriceIds(UserOrder $userOrder): array
    {
        return array_column($userOrder->order_items, 'number_of_tickets', 'stripe_price_id');
    }

    /**
     * Get the numbers of tickets
     *
     * @param UserOrder $userOrder
     * @return int[]
     */
    public static function getNumbersOfTickets(UserOrder $userOrder): array
    {
        return array_column($userOrder->order_items, 'number_of_tickets', 'ticket_id');
    }

    /**
     * Create order items
     *
     * @param Ticket[] $tickets
     * @param int[] $numbersOfTickets
     * @return array
     */
    public static function createOrderItems(array $tickets, array $numbersOfTickets): array
    {
        $orderItems = [];
        foreach ($tickets as $ticket) {
            $orderItems[] = UserOrder::createOrderItem($ticket, $numbersOfTickets[$ticket->id]);
        }

        return $orderItems;
    }

    /**
     * Create user tickets
     *
     * @param UserOrder $userOrder
     * @return UserTicket[]
     */
    public static function createUserTickets(UserOrder $userOrder): array
    {
        $userTickets = [];
        foreach ($userOrder->order_items as $orderItem) {
            for ($i = 0; $i < $orderItem['number_of_tickets']; $i++) {
                $userTickets[] = new UserTicket([
                    'user_id' => $userOrder->user_id,
                    'ticket_id' => $orderItem['ticket_id'],
                    'used_at' => null,
                ]);
            }
        }

        return $userTickets;
    }

    /**
     * Increase the numbers of reserved tickets
     *
     * @param Ticket[] $tickets
     * @param int[] $numbersOfTickets
     * @return void
     */
    public static function increaseNumbersOfReservedTickets(array $tickets, array $numbersOfTickets): void
    {
        foreach ($tickets as $ticket) {
            $ticket->number_of_reserved_tickets += $numbersOfTickets[$ticket->id];
        }
    }

    /**
     * Decrease the numbers of tickets
     *
     * @param Ticket[] $tickets
     * @param int[] $numbersOfTickets
     * @return void
     */
    public static function decreaseNumbersOfTickets(array $tickets, array $numbersOfTickets): void
    {
        foreach ($tickets as $ticket) {
            if ($numbersOfTickets[$ticket->id] > $ticket->number_of_tickets) {
                throw new RuntimeException("Not enough tickets available. ticket_id: {$ticket->id}, number_of_tickets: {$ticket->number_of_tickets}, used_number_of_tickets: {$numbersOfTickets[$ticket->id]}");
            }
            
            $ticket->number_of_tickets -= $numbersOfTickets[$ticket->id];
        }
    }

    /**
     * Decrease the numbers of reserved tickets
     *
     * @param Ticket[] $tickets
     * @param int[] $numbersOfTickets
     * @return void
     */
    public static function decreaseNumbersOfReservedTickets(array $tickets, array $numbersOfTickets): void
    {
        foreach ($tickets as $ticket) {
            if ($numbersOfTickets[$ticket->id] > $ticket->number_of_reserved_tickets) {
                throw new RuntimeException("Not enough reserved tickets available. ticket_id: {$ticket->id}, number_of_reserved_tickets: {$ticket->number_of_reserved_tickets}, used_number_of_tickets: {$numbersOfTickets[$ticket->id]}");
            }
            
            $ticket->number_of_reserved_tickets -= $numbersOfTickets[$ticket->id];
        }
    }

    /**
     * Checkout
     *
     * @param StripeService $stripeService
     * @param User $user
     * @param UserOrder $userOrder
     * @return Checkout
     */
    public static function checkout(StripeService $stripeService, User $user, UserOrder $userOrder): Checkout
    {
        try {
            return $stripeService->checkout($user, CheckoutService::getStripePriceIds($userOrder), ['user_order_id' => $userOrder->id]);
        } catch (Throwable $e) {
            CancelOrder::dispatch($userOrder->id);
            throw $e;
        }
    }
}
