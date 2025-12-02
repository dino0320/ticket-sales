<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\UserCart;
use App\Models\UserOrder;
use App\Models\UserTicket;

class CheckoutService
{
    /**
     * Get order items
     *
     * @param UserCart[] $userCarts
     * @param Ticket[] $tickets
     * @return array
     */
    public static function getOrderItems(array $userCarts, array $tickets): array
    {
        if ($userCarts === []) {
            return [];
        }

        $userCarts = array_column($userCarts, null, 'ticket_id');

        $orderItems = [];
        foreach ($tickets as $ticket) {
            $orderItems[] = [
                'ticket_id' => $ticket->id,
                'event_title' => $ticket->event_title,
                'event_description' => $ticket->event_description,
                'price' => $ticket->price,
                'stripe_price_id' => $ticket->stripe_price_id,
                'number_of_tickets' => $userCarts[$ticket->id]->number_of_tickets,
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
     * Create user tickets
     *
     * @param UserOrder $userOrder
     * @return UserTicket[]
     */
    public static function createUserTickets(UserOrder $userOrder): array
    {
        $userTickets = [];
        foreach ($userOrder->order_items as $orderItem) {
            $userTickets[] = new UserTicket([
                'user_id' => $userOrder->user_id,
                'ticket_id' => $orderItem['ticket_id'],
                'number_of_tickets' => $orderItem['number_of_tickets'],
            ]);
        }

        return $userTickets;
    }
}
