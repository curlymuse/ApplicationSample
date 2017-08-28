<?php

namespace App\Transformers\RoomRequestDate;

use App\Transformers\Transformer;

class RoomRequestDateTransformer extends Transformer
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
        return (object)[
            'id'        => $object->id,
            'name'      => $object->room_type_name,
            'preferred_rate_min'    => $object->preferred_rate_min,
            'preferred_rate_max'    => $object->preferred_rate_max,
            'rooms'     => $object->rooms_requested,
            'date'      => $object->room_date->format('Y-m-d'),
        ];
    }
}