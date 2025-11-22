<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of Model
 */
abstract class Repository
{
    /**
     * Model class name
     * 
     * @var class-string<TModel>
     */
    protected string $modelName;

    /**
     * Select by id
     *
     * @param integer $id
     * @return TModel
     */
    public function selectById(int $id): Model
    {
        return $this->modelName::where('id', $id)->first();
    }

    /**
     * Select by ids
     *
     * @param int[] $ids
     * @return TModel[]
     */
    public function selectByIds(array $ids): array
    {
        return $this->modelName::whereIn('id', $ids)->get()->all();
    }

    /**
     * Save
     *
     * @param TModel $model
     * @return boolean
     */
    public function save(Model $model): bool
    {
        return $model->save();
    }

    /**
     * Upsert
     *
     * @param TModel[] $models
     * @return void
     */
    public function upsert(array $models): void
    {
        $values = $update = [];
        foreach ($models as $model) {
            $attributes = $model->getAttributes();
            unset($attributes[$model->getCreatedAtColumn()], $attributes[$model->getUpdatedAtColumn()]);
            $values[] = $attributes;
            $update = array_merge($update, $model->getDirty());
        }

        $update = $update === [] ? null : array_keys($update);
        $this->modelName::upsert($values, [(new $this->modelName)->getKeyName()], $update);
    }

    /**
     * Delete
     *
     * @param TModel $model
     * @return boolean|null
     */
    public function delete(Model $model): ?bool
    {
        return $model->delete();
    }

    /**
     * Delete multiple
     *
     * @param TModel[] $models
     * @return boolean|null
     */
    public function deleteMultiple(array $models): ?bool
    {
        return $this->modelName::whereIn('id', array_column($models, 'id'))->delete();
    }
}