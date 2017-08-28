<?php

namespace App\Transformers\Reservation;

use App\Transformers\Transformer;

class ReservationForHotelTransformer extends Transformer
{
    /**
     * @var ReservationTransformer
     */
    private $parentTransformer;

    /**
     * ReservationForHotelTransformer constructor.
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

        $base->guests = collect($base->guests)->map(function($item) {
            return (object)collect($item)
                ->except('notes_internal')
                ->toArray();
        })->toArray();

        $base->guest_attributes = (object)collect($base->guest_attributes)
            ->except('notes_internal')
            ->toArray();

        return $base;
    }
}