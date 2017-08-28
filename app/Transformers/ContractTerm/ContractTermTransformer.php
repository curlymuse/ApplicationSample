<?php

namespace App\Transformers\ContractTerm;

use App\Transformers\Transformer;

class ContractTermTransformer extends Transformer
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
            'description',
            'title',
        ])->merge([
            //  Associative array of properties to pull from calculations
        ])->toArray();
    }
}