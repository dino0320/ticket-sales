<?php

namespace App\Services;

use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Pagination\CursorPaginator;

class TicketService
{
    /**
     * Get paginated tickets response
     *
     * @param CursorPaginator $tickets
     * @return array
     */
    public static function getPaginatedTicketsResponse(CursorPaginator $tickets): array
    {
        $ticketsResponse = [
            'data' => self::getTicketsResponse($tickets->getCollection()->all()),
            'prev_page_url' => $tickets->previousPageUrl(),
            'next_page_url' => $tickets->nextPageUrl(),
        ];

        return $ticketsResponse;
    }

    /**
     * Get tickets response
     *
     * @param Ticket[] $tickets
     * @return array
     */
    private static function getTicketsResponse(array $tickets): array
    {
        $ticketsResponse = [];
        foreach ($tickets as $ticket) {
            $ticketsResponse[] = self::getTicketResponse($ticket);
        }

        return $ticketsResponse;
    }

    /**
     * Get ticket response
     *
     * @param Ticket $ticket
     * @return array
     */
    public static function getTicketResponse(Ticket $ticket): array
    {
        return [
            'id' => $ticket->id,
            'event_title' => $ticket->event_title,
            'event_description' => $ticket->event_description,
            'price' => $ticket->price,
            'event_start_date' => $ticket->event_start_date,
            'event_end_date' => $ticket->event_end_date,
        ];
    }

    /**
     * Get issued ticket response
     *
     * @param Ticket $ticket
     * @return array
     */
    public static function getIssuedTicketResponse(Ticket $ticket): array
    {
        return [
            'id' => $ticket->id,
            'event_title' => $ticket->event_title,
            'event_description' => $ticket->event_description,
            'price' => $ticket->price,
            'number_of_tickets' => $ticket->number_of_tickets,
            'event_start_date' => $ticket->event_start_date,
            'event_end_date' => $ticket->event_end_date,
            'start_date' => $ticket->start_date,
            'end_date' => $ticket->end_date,
        ];
    }

    /**
     * Weather a ticket is during the period
     *
     * @param Ticket $ticket
     * @return boolean
     */
    public static function isDuringPeriod(Ticket $ticket): bool
    {
        $now = new Carbon();
        return $now >= $ticket->start_date && $now <= $ticket->end_date;
    }
}
