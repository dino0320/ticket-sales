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
     * Select by user_id
     *
     * @param integer $userId
     * @return UserTicket[]
     */
    public function selectByUserId(int $userId): array
    {
        return UserTicket::where('user_id', $userId)->get()->all();
    }
}