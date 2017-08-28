<?php

namespace App\Repositories\Contracts;

interface EventLocationRepositoryInterface
{
    /**
     * Get collection of locations that have a place ID in this array
     *
     * @param array $placeIds
     *
     * @return mixed
     */
    public function getWherePlaceIdIn($placeIds = []);

    /**
     * Create a new EventLocation or update existing one using Google place ID
     *
     * @param int $placeId
     * @param array $attributes
     *
     * @return mixed
     */
    public function createOrUpdateUsingPlaceId($placeId, $attributes = []);
}