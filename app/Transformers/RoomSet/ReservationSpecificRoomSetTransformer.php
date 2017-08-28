<?php

namespace App\Transformers\RoomSet;

use App\Transformers\Transformer;
use Illuminate\Support\Collection;

class ReservationSpecificRoomSetTransformer extends Transformer
{
    /**
     * Transform a single object
     *
     * @param $object
     *
     * @return mixed
     */
    public function transform($object)
    {
        return (object)collect($object)->only([
            //  Numeric array of fields to copy directly from object
        ])->merge([
            //  Associative array of properties to pull from calculations
        ])->toArray();
    }

    /**
     * @param Collection $objects
     * @return array
     */
    public function transformCollection(Collection $objects)
    {
        $return = [];

        foreach ($objects->pluck('name')->unique() as $roomType) {
            $roomSets = $objects->where('name', $roomType)->sortBy('reservation_date');

            $typeCollection = (object)[
                'room_type_name'    => $roomType,
                'room_type_description' => $roomSets->first()->description,
                'dates' => [],
            ];

            foreach ($roomSets as $set) {
                $typeCollection->dates[] = (object)[
                    'room_set_id'   => $set->id,
                    'rate'  => $set->rate,
                    'date'  => $set->reservation_date->format('Y-m-d'),
                ];
            }

            $return[] = $typeCollection;
        }

        return $return;
    }
}