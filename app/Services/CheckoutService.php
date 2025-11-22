<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\UserCart;

class CheckoutService
{
    /**
     * Get stripe price ids
     *
     * @param UserCart[] $userCarts
     * @param Ticket[] $tickets
     * @return int[]
     */
    public static function getStripePriceIds(array $userCarts, array $tickets): array
    {
        if ($userCarts === []) {
            return [];
        }

        $userCarts = array_column($userCarts, null, 'ticket_id');

        $stripe_price_ids = [];
        foreach ($tickets as $ticket) {
            $stripe_price_ids[$ticket->stripe_price_id] = ($stripe_price_ids[$ticket->stripe_price_id] ?? 0) + $userCarts[$ticket->id]->number_of_tickets;
        }

        return $stripe_price_ids;
    }
}
