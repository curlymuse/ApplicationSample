<?php

namespace App\Transformers\Clause;

use App\Transformers\Transformer;

class ClauseTransformer extends Transformer
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
        return (object)(
            collect($object)->only(
                'id',
                'title',
                'body'
            )->merge([
                'is_default'    => (bool)($object->is_default)
            ])->toArray()
        );
    }
}