<?php

namespace App\Transformers\Guest;

use App\Transformers\Reservation\GuestSpecificReservationTransformer;
use App\Transformers\Transformer;

class GuestProfileTransformer extends Transformer
{
    /**
     * @var BasicGuestTransformer
     */
    private $parentTransformer;

    /**
     * @var GuestSpecificReservationTransformer
     */
    private $reservationTransformer;

    /**
     * ReservationSpecificGuestTransformer constructor.
     * @param BasicGuestTransformer $parentTransformer
     * @param GuestSpecificReservationTransformer $reservationTransformer
     */
    public function __construct(
        BasicGuestTransformer $parentTransformer,
        GuestSpecificReservationTransformer $reservationTransformer
    )
    {
        $this->parentTransformer = $parentTransformer;
        $this->reservationTransformer = $reservationTransformer;
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

        return (object)collect($base)->merge([
            'special_requests'  => $object->special_requests,
            'notes_to_hotel'  => $object->notes_to_hotel,
            'notes_internal'  => $object->notes_internal,
            'reservations'  => $this->reservationTransformer->transformCollection($object->reservations),
        ])->toArray();
    }
}