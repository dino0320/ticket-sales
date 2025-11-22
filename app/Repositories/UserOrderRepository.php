<?php

namespace App\Repositories;

use App\Models\UserOrder;
use App\Repositories\Repository;

class UserOrderRepository extends Repository
{
    /**
     * Model class name
     */
    protected string $modelName = UserOrder::class;
}