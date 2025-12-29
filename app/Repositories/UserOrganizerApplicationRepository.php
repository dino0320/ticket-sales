<?php

namespace App\Repositories;

use App\Consts\PaginationConst;
use App\Models\UserOrganizerApplication;
use App\Repositories\Repository;
use Illuminate\Pagination\LengthAwarePaginator;

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
     * @param integer $numberOfItemsPerPage
     * @return LengthAwarePaginator
     */
    public function selectByStatus(int $status, int $numberOfItemsPerPage = PaginationConst::NUMBER_OF_RECORDS_PER_PAGE): LengthAwarePaginator
    {
        return UserOrganizerApplication::where('status', $status)->orderBy('applied_at', 'asc')->paginate($numberOfItemsPerPage);
    }
}