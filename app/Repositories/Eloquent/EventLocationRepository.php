<?php

namespace App\Repositories\Eloquent;

use App\Exceptions\MalformedRepositoryInputException;
use App\Repositories\Contracts\EventLocationRepositoryInterface;

class EventLocationRepository extends Repository implements EventLocationRepositoryInterface
{
    /**
     * Get collection of locations that have a place ID in this array
     *
     * @param array $placeIds
     *
     * @return mixed
     */
    public function getWherePlaceIdIn($placeIds = [])
    {
        return $this->model
            ->whereIn('place_id', $placeIds)
            ->get();
    }

    /**
     * Create a new EventLocation or update existing one using Google place ID
     *
     * @param int $placeId
     * @param array $attributes
     *
     * @return mixed
     */
    public function createOrUpdateUsingPlaceId($placeId, $attributes = [])
    {
        $location = $this->findWhere(['place_id' => $placeId]);

        if (! $location) {
            return $this->store(
                collect($attributes)->merge([
                    'place_id'      => $placeId,
                ])->toArray()
            );
        }

        $this->update(
            $location->id,
            $attributes
        );

        return $location;
    }
}