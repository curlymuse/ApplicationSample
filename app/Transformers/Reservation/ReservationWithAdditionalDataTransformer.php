<?php

namespace App\Transformers\Reservation;

use App\Transformers\Hotel\HotelTransformer;
use App\Transformers\Transformer;

class ReservationWithAdditionalDataTransformer extends Transformer
{
    /**
     * @var ReservationTransformer
     */
    private $parentTransformer;

    /**
     * @var HotelTransformer
     */
    private $hotelTransformer;

    /**
     * ReservationWithAdditionalDataTransformer constructor.
     * @param ReservationTransformer $parentTransformer
     * @param HotelTransformer $hotelTransformer
     */
    public function __construct(
        ReservationTransformer $parentTransformer,
        HotelTransformer $hotelTransformer
    )
    {
        $this->parentTransformer = $parentTransformer;
        $this->hotelTransformer = $hotelTransformer;
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

        $contract = $object->roomSets()->first()->contract;

        return (object)collect($base)->merge([
            'hotel' => $this->hotelTransformer->transform($contract->proposal->hotel),
            'contract_id'   => $contract->id,
        ])->toArray();
    }
}