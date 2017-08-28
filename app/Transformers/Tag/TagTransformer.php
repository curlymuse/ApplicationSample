<?php

namespace App\Transformers\Tag;

use App\Transformers\Transformer;

class TagTransformer extends Transformer
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
        return $object->name;
    }
}