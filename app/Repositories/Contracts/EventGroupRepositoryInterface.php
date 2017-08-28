<?php

namespace App\Repositories\Contracts;

interface EventGroupRepositoryInterface
{
    /**
     * Get all EventGroups for a given Client
     *
     * @param $clientId
     *
     * @return mixed
     */
    public function allForClient($clientId);
}