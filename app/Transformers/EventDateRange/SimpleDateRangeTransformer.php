<?php

namespace App\Transformers\EventDateRange;

use App\Transformers\Transformer;

class SimpleDateRangeTransformer extends Transformer
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
        ])->merge([
            'start_date'    => $object->start_date->format('Y-m-d'),
            'end_date'    => $object->end_date->format('Y-m-d'),
            'check_in_date'    => $object->check_in_date->format('Y-m-d'),
            'check_out_date'    => $object->check_out_date->format('Y-m-d'),
        ])->toArray();
    }
}