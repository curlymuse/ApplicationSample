<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\EventRepositoryInterface;

class EventRepository extends Repository implements EventRepositoryInterface
{
    /**
     * Sync tags
     *
     * @param int $eventId
     * @param array $tagIds
     *
     * @return mixed
     */
    public function syncTags($eventId, $tagIds = [])
    {
        $this->find($eventId)
            ->tags()
            ->sync($tagIds);
    }

    /**
     * Get all Events for given Client
     *
     * @param int $clientId
     *
     * @return mixed
     */
    public function allForClient($clientId)
    {
        return $this->model
            ->whereClientId($clientId)
            ->get();
    }

    /**
     * Create a new Event owned by the Licensee
     *
     * @param int $licenseeId
     * @param int $clientId
     * @param array $attributes
     *
     * @return mixed
     */
    public function storeForLicenseeAndClient($licenseeId, $clientId, $attributes = [])
    {
        return $this->store(
            collect($attributes)
                ->merge([
                    'licensee_id' => $licenseeId,
                    'client_id' => $clientId,
                ])
                ->toArray()
        );
    }

    /**
     * Duplicate this event with a new name
     *
     * @param int $eventId
     * @param string $name
     *
     * @return mixed
     */
    public function duplicateWithNewName($eventId, $name)
    {
        $event = $this->find($eventId);

        $newEvent = $event->replicate(['name', 'id', 'created_at', 'updated_at']);
        $newEvent->name = $name;
        $newEvent->save();

        return $newEvent;
    }

    /**
     * Does this event belong to this licensee?
     *
     * @param int $eventId
     * @param int $licenseeId
     *
     * @return mixed
     */
    public function belongsToLicensee($eventId, $licenseeId)
    {
        return $this->model
            ->whereId($eventId)
            ->whereLicenseeId($licenseeId)
            ->exists();
    }
}
