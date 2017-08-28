<?php

namespace App\Transformers\Proposal;

use App\Transformers\Hotel\HotelSimpleViewTransformer;
use App\Transformers\Transformer;

class ProposalSimpleViewTransformer extends Transformer
{
    /**
     * @var HotelSimpleViewTransformer
     */
    private $hotelTransformer;

    /**
     * ProposalSimpleViewTransformer constructor.
     * @param HotelSimpleViewTransformer $hotelTransformer
     */
    public function __construct(HotelSimpleViewTransformer $hotelTransformer)
    {
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
        return (object)collect($object)->only([
            'id',
        ])->merge([
            'event_id'  => $object->proposalRequest->event_id,
            'event'  => $object->proposalRequest->event->name,
            'hotel' => $this->hotelTransformer->transform($object->hotel),
            'created_at'    => $object->created_at->format('Y-m-d H:i:s'),
            'expires_at' => self::dateFormatOrNull($object->honor_bid_until, 'Y-m-d H:i:s'),
            'status'    => $object->present()->status,
        ])->toArray();
    }
}
