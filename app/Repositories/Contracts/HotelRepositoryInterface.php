<?php

namespace App\Repositories\Contracts;

interface HotelRepositoryInterface
{
    /**
     * Attach user to hotel as hotelier
     *
     * @param int $hotelId
     * @param int $userId
     *
     * @return mixed
     */
    public function attachHotelier($hotelId, $userId);

    /**
     * Attach a hotel to proposal request
     *
     * @param int $hotelId
     * @param int $requestId
     *
     * @return mixed
     */
    public function attachToProposalRequest($hotelId, $requestId);

    /**
     * Detach a hotel from proposal request
     *
     * @param int $hotelId
     * @param int $requestId
     *
     * @return mixed
     */
    public function detachFromProposalRequest($hotelId, $requestId);

    /**
     * Get a collection of hotels attached to this proposal request
     *
     * @param int $requestId
     *
     * @return mixed
     */
    public function allAttachedToProposalRequest($requestId);

    /**
     * Pull any existing property with the same name and similar coordinates
     *
     * @param string $name
     * @param float $latitude
     * @param float $longitude
     *
     * @return mixed
     */
    public function findPropertyMatch($name, $latitude, $longitude);

    /**
     * Find a hotel with a name resembling the passed-in string
     *
     * @param string $name
     *
     * @return mixed
     */
    public function searchByName($name);
}
