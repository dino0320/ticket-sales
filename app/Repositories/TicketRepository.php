<?php

namespace App\Repositories;

use App\Consts\CommonConst;
use App\Models\Ticket;
use App\Repositories\Repository;
use Carbon\Carbon;
use Illuminate\Pagination\CursorPaginator;

class TicketRepository extends Repository
{
    /**
     * Model class name
     */
    protected string $modelName = Ticket::class;

    /**
     * Select paginated tickets during the period
     *
     * @param Carbon $now
     * @return CursorPaginator
     */
    public function selectPaginatedTicketsDuringPeriod(Carbon $now): CursorPaginator
    {
        return Ticket::where([
                ['start_date', '<=', $now],
                ['end_date', '>=', $now],
            ])->orderBy('event_start_date', 'asc')->cursorPaginate(CommonConst::NUMBER_OF_RECORDS_PER_PAGE);
    }

    /**
     * Select paginated tickets by ids
     *
     * @param int[] $ids
     * @return CursorPaginator
     */
    public function selectPaginatedTicketsByIds(array $ids): CursorPaginator
    {
        return Ticket::whereIn('id', $ids)->cursorPaginate(CommonConst::NUMBER_OF_RECORDS_PER_PAGE);
    }

    /**
     * Select paginated tickets during the event by ids
     *
     * @param int[] $ids
     * @param Carbon $now
     * @return CursorPaginator
     */
    public function selectPaginatedTicketsDuringEventByIds(array $ids, Carbon $now): CursorPaginator
    {
        return Ticket::whereIn('id', $ids)->where('event_end_date', '>=', $now)->orderBy('event_start_date', 'asc')->cursorPaginate(CommonConst::NUMBER_OF_RECORDS_PER_PAGE);
    }
}