<?php

namespace App\Transformers\RoomSet;

use App\Transformers\Transformer;

class RoomSetTransformer extends Transformer
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
            'id',
            'name',
            'description',
            'rooms_offered',
            'rate'
        ])->merge([
            'reservation_date'  => $object->reservation_date->format('Y-m-d'),
        ])->toArray();
    }
}