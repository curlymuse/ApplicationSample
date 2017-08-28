<?php

namespace App\Transformers\SpaceRequest;

use App\Transformers\Transformer;

class SpaceRequestTransformer extends Transformer
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
            'start_time',
            'end_time',
            'name',
            'attendees',
            'budget',
            'budget_units',
            'room_type',
            'layout',
            'requests',
            'meal',
            'notes',
        ])->merge([
            'date_requested' => ($object->date_requested) ? $object->date_requested->format('Y-m-d') : null,
            'equipment' => json_decode($object->equipment),
        ])->toArray();
    }
}
