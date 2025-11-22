<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class Repository
{
    /**
     * Save
     *
     * @param Model $model
     * @return boolean
     */
    public function save(Model $model): bool
    {
        return $model->save();
    }

    /**
     * Delete
     *
     * @param Model $model
     * @return boolean|null
     */
    public function delete(Model $model): ?bool
    {
        return $model->delete();
    }
}