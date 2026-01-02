<?php

namespace App\Repositories;

use App\Consts\PaginationConst;
use App\Models\Ticket;
use App\Repositories\Repository;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class TicketRepository extends Repository
{
    /**
     * Model class name
     */
    protected string $modelName = Ticket::class;

    /**
     * Select paginated tickets during the sales period
     *
     * @param Carbon $now
     * @param integer $numberOfItemsPerPage
     * @return LengthAwarePaginator
     */
    public function selectPaginatedTicketsDuringSalesPeriod(Carbon $now, int $numberOfItemsPerPage = PaginationConst::NUMBER_OF_RECORDS_PER_PAGE): LengthAwarePaginator
    {
        return Ticket::where([
            ['start_date', '<=', $now],
            ['end_date', '>=', $now],
        ])->orderBy('event_start_date', 'asc')->orderBy('id', 'asc')->paginate($numberOfItemsPerPage);
    }

    /**
     * Select tickets during the sales period by ids for update
     *
     * @param Carbon $now
     * @param int[] $ids
     * @return Ticket[]
     */
    public function selectTicketsDuringSalesPeriodByIdsForUpdate(Carbon $now, array $ids): array
    {
        return Ticket::where([
            ['start_date', '<=', $now],
            ['end_date', '>=', $now],
        ])->whereIn('id', $ids)->lockForUpdate()->get()->all();
    }

    /**
     * Select paginated tickets during the sales period by ids
     *
     * @param Carbon $now
     * @param int[] $ids
     * @param integer $numberOfItemsPerPage
     * @return LengthAwarePaginator
     */
    public function selectPaginatedTicketsDuringSalesPeriodByIds(Carbon $now, array $ids, int $numberOfItemsPerPage = PaginationConst::NUMBER_OF_RECORDS_PER_PAGE): LengthAwarePaginator
    {
        return Ticket::where([
            ['start_date', '<=', $now],
            ['end_date', '>=', $now],
        ])->whereIn('id', $ids)->orderBy('id', 'asc')->paginate($numberOfItemsPerPage);
    }

    /**
     * Select tickets during the event by ids
     *
     * @param int[] $ids
     * @param Carbon $now
     * @param integer $numberOfItemsPerPage
     * @return Ticket[]
     */
    public function selectTicketsDuringEventByIds(array $ids, Carbon $now): array
    {
        return Ticket::where('event_end_date', '>=', $now)
            ->whereIn('id', $ids)
            ->orderBy('event_start_date', 'asc')
            ->orderBy('id', 'asc')->get()->all();
    }

    /**
     * Select paginated tickets by organizer_user_id
     *
     * @param integer $organizerUserId
     * @param integer $numberOfItemsPerPage
     * @return LengthAwarePaginator
     */
    public function selectPaginatedTicketsByOrganizerUserId(int $organizerUserId, int $numberOfItemsPerPage = PaginationConst::NUMBER_OF_RECORDS_PER_PAGE): LengthAwarePaginator
    {
        return Ticket::where('organizer_user_id', $organizerUserId)->orderBy('id', 'desc')->paginate($numberOfItemsPerPage);
    }
}