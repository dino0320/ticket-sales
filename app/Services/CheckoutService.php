<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\UserOrder;
use App\Models\UserTicket;
use RuntimeException;

class CheckoutService
{
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
            $orderItems[] = [
                'ticket_id' => $ticket->id,
                'event_title' => $ticket->event_title,
                'event_description' => $ticket->event_description,
                'price' => $ticket->price,
                'stripe_price_id' => $ticket->stripe_price_id,
                'number_of_tickets' => $numbersOfTickets[$ticket->id],
            ];
        }

        return $orderItems;
    }

    /**
     * Get stripe price ids
     *
     * @param UserOrder $userOrder
     * @return int[]
     */
    public static function getStripePriceIds(UserOrder $userOrder): array
    {
        $stripePriceIds = [];
        foreach ($userOrder->order_items as $orderItem) {
            $stripePriceIds[$orderItem['stripe_price_id']] = ($stripePriceIds[$orderItem['stripe_price_id']] ?? 0) + $orderItem['number_of_tickets'];
        }

        return $stripePriceIds;
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
     * Decrease the numbers of tickets
     *
     * @param Ticket[] $tickets
     * @param int[] $numbersOfTickets
     * @return void
     */
    public static function decreaseNumbersOfTickets(array $tickets, array $numbersOfTickets): void
    {
        foreach ($tickets as $ticket) {
            if (($ticket->number_of_tickets - $numbersOfTickets[$ticket->id]) <= 0) {
                throw new RuntimeException("The number of tickets is 0 or less. ticket_id: {$ticket->id}");
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
            if (($ticket->number_of_reserved_tickets - $numbersOfTickets[$ticket->id]) < 0) {
                throw new RuntimeException("The number of reserved tickets is less than 0. ticket_id: {$ticket->id}");
            }
            
            $ticket->number_of_reserved_tickets -= $numbersOfTickets[$ticket->id];
        }
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
}
