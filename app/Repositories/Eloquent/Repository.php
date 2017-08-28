<?php

namespace App\Repositories\Eloquent;

abstract class Repository
{
    /**
     * @var
     */
    protected $model;

    /**
     * @param $model
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * Return all models in a collection
     *
     * @return mixed
     */
    public function all()
    {
        return $this->model->all();
    }

    /**
     * Create a new record
     *
     * @param $attributes
     *
     * @return mixed
     */
    public function store($attributes)
    {
        return $this->model->create($attributes);
    }

    /**
     * Update an existing model
     *
     * @param $id
     * @param $attributes
     *
     * @return mixed
     */
    public function update($id, $attributes)
    {
        return $this->model
            ->whereId($id)
            ->update($attributes);
    }

    /**
     * Delete a model
     *
     * @param $id
     *
     * @return mixed
     */
    public function delete($id)
    {
        return $this->model
            ->destroy($id);
    }

    /**
     * Get a model with a specific idea
     *
     * @param $id
     *
     * @return mixed
     */
    public function find($id)
    {
        return $this->model->find($id);
    }

    /**
     * Search for object with supplied properties, and return
     * either the first object or null
     *
     * @param array $cols
     *
     * @return mixed
     */
    public function findWhere($cols = [])
    {
        return $this->model
            ->where($cols)
            ->first();
    }

    /**
     * Return models with an ID in the supplied array
     *
     * @param array $ids
     *
     * @return mixed
     */
    public function getWhereIdIn($ids = [])
    {
        return $this->model
            ->whereIn('id', $ids)
            ->get();
    }

    /**
     * Determine if object with the given ID belongs to a foreign
     * object with another ID
     *
     * @param int $id
     * @param string $foreignIdColumn
     * @param int $foreignId
     *
     * @return bool
     */
    public function belongsTo($id, $foreignIdColumn, $foreignId)
    {
        return $this->model
            ->whereId($id)
            ->where($foreignIdColumn, $foreignId)
            ->exists();
    }

    /**
     * Check if a model with this ID exists
     *
     * @param $id
     *
     * @return bool
     */
    public function exists($id)
    {
        return $this->model
            ->whereId($id)
            ->exists();
    }

    /**
     * Determine if a model with a specific column value exists
     *
     * @param mixed $col
     * @param mixed $value
     *
     * @return bool
     */
    public function existsWhere($col, $value = null)
    {
        return $this->model
            ->where($col, $value)
            ->exists();
    }
}
