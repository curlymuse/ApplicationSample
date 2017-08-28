<?php

namespace App\Repositories\Contracts;

interface ClientRepositoryInterface
{
    /**
     * Find a client with this place ID, or else create one and return the ID
     *
     * @param $placeId
     * @param $attributes
     * @param bool $newObjectCreated
     *
     * @return mixed
     */
    public function findOrCreateWithPlaceId($placeId, $attributes, &$newObjectCreated = false);

    /**
     * Update the client with this place ID
     *
     * @param string $placeId
     * @param array $attributes
     *
     * @return mixed
     */
    public function updateWithPlaceId($placeId, $attributes = []);

    /**
     * Get all clients attached to this licensee
     *
     * @param int $licenseeId
     *
     * @return mixed
     */
    public function allForLicensee($licenseeId);
}