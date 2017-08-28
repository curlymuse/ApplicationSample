<?php

namespace App\Transformers\EventDateRange;

use App\Models\SpaceRequest;
use App\Transformers\RoomRequestDate\RoomRequestDateTransformer;
use App\Transformers\SpaceRequest\SpaceRequestTransformer;
use App\Transformers\Transformer;

class EventDateRangeTransformer extends Transformer
{
    /**
     * @var RoomRequestDateTransformer
     */
    private $roomRequestTransformer;

    /**
     * @var SpaceRequestTransformer
     */
    private $spaceRequestTransformer;

    /**
     * EventDateRangeTransformer constructor.
     * @param RoomRequestDateTransformer $roomRequestTransformer
     * @param SpaceRequestTransformer $spaceRequestTransformer
     */
    public function __construct(
        RoomRequestDateTransformer $roomRequestTransformer,
        SpaceRequestTransformer $spaceRequestTransformer
    )
    {
        $this->roomRequestTransformer = $roomRequestTransformer;
        $this->spaceRequestTransformer = $spaceRequestTransformer;
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
        return (object)[
            'id'            => $object->id,
            'start_date'    => $object->start_date->format('Y-m-d'),
            'end_date'    => $object->end_date->format('Y-m-d'),
            'check_in_date'    => $object->check_in_date->format('Y-m-d'),
            'check_out_date'    => $object->check_out_date->format('Y-m-d'),
            'room_request_dates'    => $this->roomRequestTransformer->transformCollection($object->roomRequestDates),
            'space_requests'    => $this->spaceRequestTransformer->transformCollection(
                $object->spaceRequests()->whereType('Meeting')->get()
            ),
            'food_beverage_requests'    => $this->spaceRequestTransformer->transformCollection(
                $object->spaceRequests()->whereType('Food & Beverage')->get()
            ),
        ];
    }
}