<?php

namespace App\Transformers\User;

use App\Transformers\Hotel\HotelTransformer;
use App\Transformers\Transformer;

class HotelierUserTransformer extends Transformer
{
    /**
     * @var HotelTransformer
     */
    private $hotelTransformer;

    /**
     * @var UserTransformer
     */
    private $parentTransformer;

    /**
     * HotelierUserTransformer constructor.
     * @param HotelTransformer $hotelTransformer
     * @param UserTransformer $parentTransformer
     */
    public function __construct(
        HotelTransformer $hotelTransformer,
        UserTransformer $parentTransformer
    )
    {
        $this->hotelTransformer = $hotelTransformer;
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
            'hotels'    => $this->hotelTransformer->transformCollection($object->hotels)
        ])->toArray();
    }
}