<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\RoomSetRepositoryInterface;
use App\Repositories\Eloquent\Repository;

class RoomSetRepository extends Repository implements RoomSetRepositoryInterface
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
    public function addToContract($contract, $date, $rooms, $rate, $name, $metaData = [])
    {
        $insertData = collect([
            'reservation_date'  => $date,
            'rooms_offered' => $rooms,
            'name'  => $name,
            'rate'  => $rate,
        ])->merge($metaData)->toArray();

        $contract->roomSets()->save(
            new $this->model($insertData)
        );
    }

    /**
     * Do the room sets with these IDs have the same name?
     *
     * @param array $ids
     *
     * @return mixed
     */
    public function haveSameName($ids = [])
    {
        return $this->model
            ->whereIn('id', $ids)
            ->pluck('name')
            ->unique()
            ->count() <= 1;
    }
}
