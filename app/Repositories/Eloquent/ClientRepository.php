<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\ClientRepositoryInterface;

class ClientRepository extends Repository implements ClientRepositoryInterface
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
    public function findOrCreateWithPlaceId($placeId, $attributes, &$newObjectCreated = false)
    {
        $client = $this->model
            ->wherePlaceId($placeId)
            ->first();

        if (! $client) {
            $data = collect($attributes)
                ->merge([
                    'place_id' => $placeId
                ])->toArray();
            $client = $this->store($data);
            $newObjectCreated = true;
        }

        return $client;
    }

    /**
     * Update the client with this place ID
     *
     * @param string $placeId
     * @param array $attributes
     *
     * @return mixed
     */
    public function updateWithPlaceId($placeId, $attributes = [])
    {
        $this->model
            ->wherePlaceId($placeId)
            ->firstOrFail()
            ->update($attributes);
    }

    /**
     * Get all clients attached to this licensee
     *
     * @param int $licenseeId
     *
     * @return mixed
     */
    public function allForLicensee($licenseeId)
    {
        return $this->model
            ->whereHas('events', function($query) use ($licenseeId) {
                $query->whereLicenseeId($licenseeId);
            })->get();
    }
}
