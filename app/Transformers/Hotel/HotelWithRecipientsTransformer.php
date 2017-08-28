<?php

namespace App\Transformers\Hotel;

use App\Transformers\Transformer;
use App\Transformers\User\UserTransformer;

class HotelWithRecipientsTransformer extends Transformer
{
    /**
     * @var HotelTransformer
     */
    private $hotelTransformer;

    /**
     * @var UserTransformer
     */
    private $userTransformer;

    /**
     * HotelWithRecipientsTransformer constructor.
     * @param HotelTransformer $hotelTransformer
     * @param UserTransformer $userTransformer
     */
    public function __construct(
        HotelTransformer $hotelTransformer,
        UserTransformer $userTransformer
    )
    {
        $this->hotelTransformer = $hotelTransformer;
        $this->userTransformer = $userTransformer;
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
        $base = $this->hotelTransformer->transform($object);
        $base->recipients = $this->userTransformer->transformCollection($object->recipients);

        return $base;
    }
}
