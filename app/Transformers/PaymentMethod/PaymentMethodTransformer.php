<?php

namespace App\Transformers\PaymentMethod;

use App\Transformers\Transformer;

class PaymentMethodTransformer extends Transformer
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
            'title',
        ])->merge([
            //  Associative array of properties to pull from calculations
        ])->toArray();
    }
}