<?php

namespace App\Services;

use App\Models\Ticket;
use InvalidArgumentException;

class TicketService
{
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
}
