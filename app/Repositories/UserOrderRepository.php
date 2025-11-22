<?php

namespace App\Repositories;

use App\Models\UserOrder;
use App\Repositories\Repository;

class UserOrderRepository extends Repository
{
    /**
     * Select by id
     *
     * @param integer $id
     * @return UserOrder
     */
    public function selectById(int $id): UserOrder
    {
        return UserOrder::where('id', $id)->first();
    }
}