<?php

namespace App\Services;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;

class CartService
{
    /**
     * Get the number of tickets
     *
     * @param Collection $tickets
     * @return int[]
     */
    public static function getNumberOfTickets(Collection $userCarts): array
    {
        return array_column($userCarts->all(), 'number_of_tickets', 'ticket_id');
    }

    /**
     * Get the total price of tickets
     *
     * @param Collection $tickets
     * @param int[] $numberOfTickets
     * @return integer
     */
    public static function getTotalPrice(Collection $tickets, array $numberOfTickets): int
    {
        $totalPrice = 0;
        foreach ($tickets as $ticket) {
            $totalPrice += $ticket->price * $numberOfTickets[$ticket->id];
        }

        return $totalPrice;
    }

    /**
     * Check if the given number is not less than 0 or more than the number of tickets
     *
     * @param integer $numberOfTickets
     * @param Ticket $ticket
     * @return void
     */
    public static function checkIfNumberOfTicketsIsValid(int $numberOfTickets, Ticket $ticket): void
    {
        if ($numberOfTickets <= 0 || $numberOfTickets > $ticket->number_of_tickets) {
            throw new InvalidArgumentException("Invalid number_of_tickets. number_of_tickets: {$numberOfTickets}");
        }
    }

    /**
     * Get the difference in the total price of tickets
     *
     * @param integer $preNumberOfTickets
     * @param integer $numberOfTickets
     * @param Ticket $ticket
     * @return integer
     */
    public static function getDifferenceInTotalPrice(int $preNumberOfTickets, int $numberOfTickets, Ticket $ticket): int
    {
        return ($preNumberOfTickets - $numberOfTickets) * $ticket->price;
    }
}
