<?php

namespace App\Transformers\EventLocation;

use App\Transformers\Transformer;

class EventLocationTransformer extends Transformer
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
            'place_id',
            'latitude',
            'longitude',
            'formatted_address',
            'street_number',
            'route',
            'locality',
            'administrative_area_level_1',
            'administrative_area_level_2',
            'postal_code',
            'postal_code_suffix',
            'country',
        ])->toArray();
    }
}