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
     * Select tickets during sales period by ids for update
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
     * Select tickets whose events have not ended
     *
     * @param int[] $ids
     * @param Carbon $now
     * @return Ticket[]
     */
    public function selectTicketsWhereEventIsNotOver(array $ids, Carbon $now): array
    {
        return Ticket::where('event_end_date', '>=', $now)
            ->whereIn('id', $ids)
            ->orderBy('event_start_date', 'asc')
            ->orderBy('id', 'asc')->get()->all();
    }

    /**
     * Select ticket during event by id and organizer_user_id
     *
     * @param integer $id
     * @param integer $organizerUserId
     * @param Carbon $now
     * @return Ticket|null
     */
    public function selectTicketDuringEventByIdAndOrganizerUserId(int $id, int $organizerUserId, Carbon $now): ?Ticket
    {
        return Ticket::where('id', $id)
            ->where([
                ['organizer_user_id', $organizerUserId],
                ['event_start_date', '<=', $now],
                ['event_end_date', '>=', $now],
            ])
            ->first();
    }

    /**
     * Select ticket whose event has not ended
     *
     * @param integer $id
     * @param Carbon $now
     * @return Ticket|null
     */
    public function selectTicketWhereEventIsNotOver(int $id, Carbon $now): ?Ticket
    {
        return Ticket::where('id', $id)->where('event_end_date', '>=', $now)->first();
    }

    /**
     * Select paginated tickets during sales period
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
     * Select paginated tickets during sales period by ids
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