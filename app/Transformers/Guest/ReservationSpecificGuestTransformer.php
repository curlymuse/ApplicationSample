<?php

namespace App\Transformers\Guest;

use App\Transformers\Transformer;

class ReservationSpecificGuestTransformer extends Transformer
{
    /**
     * @var BasicGuestTransformer
     */
    private $parentTransformer;

    /**
     * ReservationSpecificGuestTransformer constructor.
     * @param BasicGuestTransformer $parentTransformer
     */
    public function __construct(BasicGuestTransformer $parentTransformer)
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

        return (object)collect($base)->merge([
            'is_primary'    => $object->pivot->is_primary,
            'payment_type'    => $object->pivot->payment_type,
            'notes_to_hotel'    => $object->pivot->notes_to_hotel,
            'notes_internal'    => $object->pivot->notes_internal,
            'special_requests'    => $object->pivot->special_requests,
            'created_at'    => $object->created_at->format('Y-m-d H:i:s'),
            'updated_at'    => $object->created_at->format('Y-m-d H:i:s'),
        ])->toArray();
    }
}