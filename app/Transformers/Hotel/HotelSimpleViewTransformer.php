<?php

namespace App\Transformers\Hotel;

use App\Transformers\Transformer;

class HotelSimpleViewTransformer extends Transformer
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
        ])->toArray();
    }
}
