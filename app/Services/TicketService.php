<?php

namespace App\Services;

use App\Models\Ticket;
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
}
