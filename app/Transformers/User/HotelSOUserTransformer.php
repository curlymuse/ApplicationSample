<?php

namespace App\Transformers\User;

use App\Transformers\Brand\SubBrandTransformer;
use App\Transformers\Transformer;

class HotelSOUserTransformer extends Transformer
{
    /**
     * @var SubBrandTransformer
     */
    private $brandTransformer;

    /**
     * @var UserTransformer
     */
    private $parentTransformer;

    /**
     * HotelSOUserTransformer constructor.
     * @param SubBrandTransformer $brandTransformer
     * @param UserTransformer $parentTransformer
     */
    public function __construct(
        SubBrandTransformer $brandTransformer,
        UserTransformer $parentTransformer
    )
    {
        $this->brandTransformer = $brandTransformer;
        $this->parentTransformer = $parentTransformer;
    }

    /**
     * Transform a single object
     *
     * @param $object
     *
     * @return mixed
     */
    public function transform($object)
    {
        $base = $this->parentTransformer->transform($object);

        return (object)collect($base)->except([
            'is_claimed'
        ])->merge([
            'is_temp_password'  => $object->is_temp_password,
            'brands'   => $this->brandTransformer->transformCollection(collect([$object->brand])),
        ])->toArray();
    }
}