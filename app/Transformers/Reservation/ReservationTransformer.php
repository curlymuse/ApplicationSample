<?php

namespace App\Transformers\Reservation;

use App\Transformers\Guest\ReservationSpecificGuestTransformer;
use App\Transformers\RoomSet\ReservationSpecificRoomSetTransformer;
use App\Transformers\Transformer;

class ReservationTransformer extends Transformer
{
    /**
     * @var ReservationSpecificGuestTransformer
     */
    private $guestTransformer;

    /**
     * @var ReservationSpecificRoomSetTransformer
     */
    private $roomSetTransformer;

    /**
     * ReservationTransformer constructor.
     * @param ReservationSpecificGuestTransformer $guestTransformer
     * @param ReservationSpecificRoomSetTransformer $roomSetTransformer
     */
    public function __construct(
        ReservationSpecificGuestTransformer $guestTransformer,
        ReservationSpecificRoomSetTransformer $roomSetTransformer
    )
    {
        $this->guestTransformer = $guestTransformer;
        $this->roomSetTransformer = $roomSetTransformer;
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
        return (object)collect($object)->only([
            'status',
            'confirmation_number',
            'cancellation_number',
        ])->merge([
            'guests'        => ($object->guests) ? $this->guestTransformer->transformCollection($object->guests) : null,
            'room_sets'     => ($object->roomSets) ? $this->roomSetTransformer->transformCollection($object->roomSets) : null,
            'guest_attributes'  => (object)[
                'name'          => $object->guest_name,
                'address'          => $object->guest_address,
                'city'          => $object->guest_city,
                'state'          => $object->guest_state,
                'country'          => $object->guest_country,
                'zip'          => $object->guest_zip,
                'phone'          => $object->guest_phone,
                'special_requests'          => $object->guest_special_requests,
                'notes_to_hotel'          => $object->guest_notes_to_hotel,
                'notes_internal'          => $object->guest_notes_internal,
            ],
            'created_at'    => $object->updated_at->format('Y-m-d H:i:s'),
            'updated_at'    => $object->updated_at->format('Y-m-d H:i:s'),
        ])->toArray();
    }
}
