<?php

namespace App\Repositories;

use App\Consts\PaginationConst;
use App\Models\UserTicket;
use App\Repositories\Repository;
use Illuminate\Pagination\LengthAwarePaginator;

class UserTicketRepository extends Repository
{
    /**
     * Model class name
     */
    protected string $modelName = UserTicket::class;

    /**
     * Select by user_id and ticket_id
     *
     * @param integer $userId
     * @param integer $ticketId
     * @return UserTicket
     */
    public function selectByUserIdAndTicketId(int $userId, int $ticketId): UserTicket
    {
        return UserTicket::where([
            ['user_id', $userId],
            ['ticket_id', $ticketId]
        ])->first();
    }

    /**
     * Select paginated not used tickets by user_id
     *
     * @param integer $userId
     * @param integer $numberOfItemsPerPage
     * @return LengthAwarePaginator
     */
    public function selectPaginatedNotUsedTicketsByUserId(int $userId, int $numberOfItemsPerPage = PaginationConst::NUMBER_OF_RECORDS_PER_PAGE): LengthAwarePaginator
    {
        return UserTicket::where('user_id', $userId)->whereNull('used_at')->paginate($numberOfItemsPerPage);
    }
}