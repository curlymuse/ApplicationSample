<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\EventGroupRepositoryInterface;

class EventGroupRepository extends Repository implements EventGroupRepositoryInterface
{

    /**
     * Get all EventGroups for a given Client
     *
     * @param $clientId
     *
     * @return mixed
     */
    public function allForClient($clientId)
    {
        return $this->model
            ->whereClientId($clientId)
            ->get();
    }
}