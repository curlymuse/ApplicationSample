<?php

namespace App\Presenters;

use Illuminate\Database\Eloquent\Model;

abstract class Presenter
{
    /**
     * @var Model
     */
    protected $entity;

    /**
     * Presenter constructor.
     *
     * @param Model $entity
     */
    public function __construct($entity)
    {
        $this->entity = $entity;
    }

    public function __get($property)
    {
        if (method_exists($this, $property)) {
            return $this->$property();
        }

        return $this->entity->$property;
    }
}