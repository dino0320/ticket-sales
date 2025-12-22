<?php

namespace App\Repositories;

use App\Models\UserTicket;
use App\Repositories\Repository;

class UserTicketRepository extends Repository
{
    /**
     * Model class name
     */
    protected string $modelName = UserTicket::class;

    /**
     * Select not used tickets by user_id
     *
     * @param integer $userId
     * @return UserTicket[]
     */
    public function selectNotUsedTicketsByUserId(int $userId): array
    {
        return UserTicket::where('user_id', $userId)->whereNull('used_at')->get()->all();
    }

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
}