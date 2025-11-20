<?php

namespace App\Repositories;

use App\Models\UserCart;
use App\Repositories\Repository;
use Illuminate\Database\Eloquent\Collection;

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
     * Select by user_id
     *
     * @param integer $userId
     * @return Collection<UserCart>
     */
    public function selectByUserId(int $userId): Collection
    {
        return UserCart::where('user_id', $userId)->get();
    }

    /**
     * Save a user cart
     *
     * @param UserCart $userCart
     * @return boolean
     */
    public function save(UserCart $userCart): bool
    {
        return $userCart->save();
    }

    /**
     * Delete a user cart
     *
     * @param UserCart $userCart
     * @return boolean|null
     */
    public function delete(UserCart $userCart): ?bool
    {
        return $userCart->delete();
    }

    /**
     * Delete user carts
     *
     * @param Collection<UserCart> $userCarts
     * @return boolean|null
     */
    public function deleteUserCarts(Collection $userCarts): ?bool
    {
        return UserCart::whereIn('id', array_column($userCarts->all(), 'id'))->delete();
    }
}