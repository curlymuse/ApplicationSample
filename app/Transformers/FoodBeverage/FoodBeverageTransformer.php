<?php

namespace App\Transformers\FoodBeverage;

use App\Transformers\Transformer;

class FoodBeverageTransformer extends Transformer
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
            //'foo'      => $object->foo,
        ];
    }
}