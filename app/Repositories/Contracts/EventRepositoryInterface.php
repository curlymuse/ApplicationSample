<?php

namespace App\Repositories\Contracts;

interface EventRepositoryInterface
{
    /**
     * Sync tags
     *
     * @param int $eventId
     * @param array $tagIds
     *
     * @return mixed
     */
    public function syncTags($eventId, $tagIds = []);

    /**
     * Get all Events for given Client
     *
     * @param int $clientId
     *
     * @return mixed
     */
    public function allForClient($clientId);

    /**
     * Create a new Event owned by the Licensee
     *
     * @param int $licenseeId
     * @param int $clientId
     * @param array $attributes
     *
     * @return mixed
     */
    public function storeForLicenseeAndClient($licenseeId, $clientId, $attributes = []);

    /**
     * Duplicate this event with a new name
     *
     * @param int $eventId
     * @param string $name
     *
     * @return mixed
     */
    public function duplicateWithNewName($eventId, $name);

    /**
     * Does this event belong to this licensee?
     *
     * @param int $eventId
     * @param int $licenseeId
     *
     * @return mixed
     */
    public function belongsToLicensee($eventId, $licenseeId);
}
