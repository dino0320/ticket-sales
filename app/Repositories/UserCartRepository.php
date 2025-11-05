<?php

namespace App\Repositories;

use App\Models\UserCart;
use App\Repositories\Repository;

class UserCartRepository extends Repository
{
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
     * Save the user cart
     *
     * @param UserCart $userCart
     * @return boolean
     */
    public function save(UserCart $userCart): bool
    {
        return $userCart->save();
    }
}