<?php

namespace App\Repositories;

use App\Models\Ticket;
use App\Repositories\Repository;
use DateTime;
use Illuminate\Pagination\CursorPaginator;

class TicketRepository extends Repository
{
    /**
     * Select paginated tickets within the period
     *
     * @param DateTime $now
     * @return CursorPaginator
     */
    public function selectPaginatedTickets(DateTime $now): CursorPaginator
    {
        return Ticket::select([
                'event_title',
                'event_description',
                'price',
                'event_start_date',
                'event_end_date',
            ])->where([
                ['start_date', '>=', $now],
                ['end_date', '<=', $now],
            ])->orderBy('event_start_date', 'asc')->cursorPaginate(10);
    }
}