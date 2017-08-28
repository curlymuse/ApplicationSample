<?php

namespace App\Transformers\Reservation;

use App\Transformers\Transformer;

class GuestSpecificReservationTransformer extends Transformer
{
    /**
     * @var ReservationTransformer
     */
    private $parentTransformer;

    /**
     * GuestSpecificReservationTransformer constructor.
     * @param ReservationTransformer $parentTransformer
     */
    public function __construct(ReservationTransformer $parentTransformer)
    {
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
            'guest_attributes',
            'guests',
        ])->merge([
            'is_primary'    => $object->pivot->is_primary,
            'payment_type'    => $object->pivot->payment_type,
            'notes_to_hotel'    => $object->pivot->notes_to_hotel,
            'notes_internal'    => $object->pivot->notes_internal,
            'special_requests'    => $object->pivot->special_requests,
        ])->toArray();
    }
}