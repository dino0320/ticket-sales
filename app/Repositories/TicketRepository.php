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
     * @param integer $numberOfItemsPerPage
     * @return CursorPaginator
     */
    public function selectPaginatedTicketsDuringPeriod(Carbon $now, int $numberOfItemsPerPage = CommonConst::NUMBER_OF_RECORDS_PER_PAGE): CursorPaginator
    {
        return Ticket::where([
                ['start_date', '<=', $now],
                ['end_date', '>=', $now],
            ])->orderBy('event_start_date', 'asc')->cursorPaginate($numberOfItemsPerPage);
    }

    /**
     * Select paginated tickets by ids
     *
     * @param int[] $ids
     * @param integer $numberOfItemsPerPage
     * @return CursorPaginator
     */
    public function selectPaginatedTicketsByIds(array $ids, int $numberOfItemsPerPage = CommonConst::NUMBER_OF_RECORDS_PER_PAGE): CursorPaginator
    {
        return Ticket::whereIn('id', $ids)->cursorPaginate($numberOfItemsPerPage);
    }

    /**
     * Select paginated tickets during the event by ids
     *
     * @param int[] $ids
     * @param Carbon $now
     * @param integer $numberOfItemsPerPage
     * @return CursorPaginator
     */
    public function selectPaginatedTicketsDuringEventByIds(array $ids, Carbon $now, int $numberOfItemsPerPage = CommonConst::NUMBER_OF_RECORDS_PER_PAGE): CursorPaginator
    {
        return Ticket::whereIn('id', $ids)->where('event_end_date', '>=', $now)->orderBy('event_start_date', 'asc')->cursorPaginate($numberOfItemsPerPage);
    }

    /**
     * Select paginated tickets by organizer_user_id
     *
     * @param integer $organizerUserId
     * @param integer $numberOfItemsPerPage
     * @return CursorPaginator
     */
    public function selectPaginatedTicketsByOrganizerUserId(int $organizerUserId, int $numberOfItemsPerPage = CommonConst::NUMBER_OF_RECORDS_PER_PAGE): CursorPaginator
    {
        return Ticket::where('organizer_user_id', $organizerUserId)->cursorPaginate($numberOfItemsPerPage);
    }
}