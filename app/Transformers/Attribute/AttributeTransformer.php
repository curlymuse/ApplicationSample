<?php

namespace App\Transformers\Attribute;

use App\Transformers\Transformer;

class AttributeTransformer extends Transformer
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
            'has_numeric_entry',
        ])->merge([
            //  Associative array of properties to pull from calculations
        ])->toArray();
    }
}