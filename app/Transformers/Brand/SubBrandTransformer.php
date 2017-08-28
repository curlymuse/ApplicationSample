<?php

namespace App\Transformers\Brand;

use App\Transformers\Transformer;

class SubBrandTransformer extends Transformer
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
            'id'      => $object->id,
            'name'    => $object->name,
            'code'    => $object->code,
        ];
    }
}
