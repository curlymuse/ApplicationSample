<?php

namespace App\Repositories\Contracts;

interface RoomSetRepositoryInterface
{
    /**
     * Add a date/quantity object to a contract
     *
     * @param Object $contract
     * @param string $date
     * @param int $rooms
     * @param float $rate
     * @param string $name
     * @param array $metaData
     *
     * @return mixed
     */
    public function addToContract($contract, $date, $rooms, $rate, $name, $metaData = []);

    /**
     * Do the room sets with these IDs have the same name?
     *
     * @param array $ids
     *
     * @return mixed
     */
    public function haveSameName($ids = []);
}
