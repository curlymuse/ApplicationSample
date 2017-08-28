<?php

namespace App\Transformers\Guest;

use App\Transformers\Transformer;

class BasicGuestTransformer extends Transformer
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
            'email',
            'address',
            'city',
            'state',
            'zip',
            'phone',
        ])->merge([
            'created_at'    => $object->created_at->format('Y-m-d H:i:s'),
            'updated_at'    => $object->created_at->format('Y-m-d H:i:s'),
        ])->toArray();
    }
}