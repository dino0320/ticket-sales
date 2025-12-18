<?php

namespace App\Repositories;

use App\Consts\CheckoutConst;
use App\Consts\CommonConst;
use App\Models\UserOrder;
use App\Models\UserOrganizerApplication;
use App\Repositories\Repository;
use Illuminate\Pagination\CursorPaginator;

class UserOrganizerApplicationRepository extends Repository
{
    /**
     * Model class name
     */
    protected string $modelName = UserOrganizerApplication::class;

    /**
     * Select by user_id
     *
     * @param integer $userId
     * @return UserOrganizerApplication|null
     */
    public function selectByUserId(int $userId): ?UserOrganizerApplication
    {
        return UserOrganizerApplication::where('user_id', $userId)->first();
    }

    /**
     * Select paginated user organizer applications by status
     *
     * @param integer $status
     * @return CursorPaginator
     */
    public function selectByStatus(int $status): CursorPaginator
    {
        return UserOrganizerApplication::where('status', $status)->orderBy('applied_at', 'asc')->cursorPaginate(CommonConst::NUMBER_OF_RECORDS_PER_PAGE);
    }
}