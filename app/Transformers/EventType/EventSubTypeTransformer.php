<?php

namespace App\Transformers\EventType;

use App\Transformers\Transformer;

class EventSubTypeTransformer extends Transformer
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
            'name'      => $object->name,
            'icon'      => $object->icon,
        ];
    }
}