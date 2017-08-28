<?php

namespace App\Transformers\HotelImage;

use App\Transformers\Transformer;

class HotelImageTransformer extends Transformer
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
            'main_path',
            'thumbnail_path',
            'source_path',
            'caption',
            'display_order',
        ])->merge([
            //  Associative array of properties to pull from calculations
        ])->toArray();
    }
}