<?php

namespace App\Repositories;

use App\Models\UserCart;
use App\Repositories\Repository;

class UserCartRepository extends Repository
{
    /**
     * Model class name
     */
    protected string $modelName = UserCart::class;

    /**
     * Select by user_id and ticket_id
     *
     * @param integer $userId
     * @param integer $ticketId
     * @return UserCart|null
     */
    public function selectByUserIdAndTicketId(int $userId, int $ticketId): ?UserCart
    {
        return UserCart::where([['user_id', $userId], ['ticket_id', $ticketId]])->first();
    }

    /**
     * Select by user_id
     *
     * @param integer $userId
     * @return UserCart[]
     */
    public function selectByUserId(int $userId): array
    {
        return UserCart::where('user_id', $userId)->get()->all();
    }
}