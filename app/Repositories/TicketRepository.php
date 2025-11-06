<?php

namespace App\Repositories;

use App\Models\Ticket;
use App\Repositories\Repository;
use Carbon\Carbon;
use Illuminate\Pagination\CursorPaginator;

class TicketRepository extends Repository
{
    /**
     * Select paginated tickets within the period
     *
     * @param Carbon $now
     * @return CursorPaginator
     */
    public function selectPaginatedTickets(Carbon $now): CursorPaginator
    {
        return Ticket::select([
                'id',
                'event_title',
                'event_description',
                'price',
                'event_start_date',
                'event_end_date',
            ])->where([
                ['start_date', '<=', $now],
                ['end_date', '>=', $now],
            ])->orderBy('event_start_date', 'asc')->cursorPaginate(10);
    }

    /**
     * Select by ids
     *
     * @param array $ids
     * @return CursorPaginator
     */
    public function selectByIds(array $ids): CursorPaginator
    {
        return Ticket::select([
                'id',
                'event_title',
                'event_description',
                'price',
                'event_start_date',
                'event_end_date',
            ])->whereIn('id', $ids)->cursorPaginate(10);
    }
}