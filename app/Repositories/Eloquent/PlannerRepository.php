<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\PlannerRepositoryInterface;

class PlannerRepository extends Repository implements PlannerRepositoryInterface
{
    /**
     * Find a planner with this google ID, or else create one and return the ID
     *
     * @param $placeId
     * @param $attributes
     * @param bool $newObjectCreated
     *
     * @return mixed
     */
    public function findOrCreateWithPlaceId($placeId, $attributes, &$newObjectCreated = false)
    {
        $planner = $this->model
            ->wherePlaceId($placeId)
            ->first();

        if (! $planner) {
            $data = collect($attributes)
                ->merge([
                    'place_id' => $placeId
                ])->toArray();
            $planner = $this->store($data);
            $newObjectCreated = true;
        }

        return $planner;
    }
}