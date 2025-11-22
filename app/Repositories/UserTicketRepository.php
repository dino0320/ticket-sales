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
}