<?php

namespace App\Repositories\Contracts;

interface PlannerRepositoryInterface
{
    /**
     * Find a planner with this place ID, or else create one and return the ID
     *
     * @param $placeId
     * @param $attributes
     * @param bool $newObjectCreated
     *
     * @return mixed
     */
    public function findOrCreateWithPlaceId($placeId, $attributes, &$newObjectCreated = false);
}