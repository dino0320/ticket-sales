<?php

namespace App\Repositories;

use App\Consts\CheckoutConst;
use App\Consts\CommonConst;
use App\Models\UserOrder;
use App\Repositories\Repository;
use Illuminate\Pagination\CursorPaginator;

class UserOrderRepository extends Repository
{
    /**
     * Model class name
     */
    protected string $modelName = UserOrder::class;

    /**
     * Select paginated user orders by user_id
     *
     * @param integer $userId
     * @return CursorPaginator
     */
    public function selectPaginatedUserOrdersByUserId(int $userId): CursorPaginator
    {
        return UserOrder::where([
            ['user_id', $userId],
            ['status', CheckoutConst::ORDER_STATUS_COMPLETED],
        ])->cursorPaginate(CommonConst::NUMBER_OF_RECORDS_PER_PAGE);
    }
}