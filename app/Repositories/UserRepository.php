<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Repository;

class UserRepository extends Repository
{
    /**
     * Create a user
     *
     * @param string $name
     * @param string $email
     * @param string $password
     * @return void
     */
    public function create(string $name, string $email, string $password): User
    {
        return User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);
    }
}