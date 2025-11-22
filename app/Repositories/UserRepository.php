<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Repository;

class UserRepository extends Repository
{
    /**
     * Model class name
     */
    protected string $modelName = User::class;
}