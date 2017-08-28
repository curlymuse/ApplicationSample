<?php

namespace App\Transformers\Proposal;

use App\Transformers\Hotel\HotelSimpleViewTransformer;
use App\Transformers\Hotel\HotelTransformer;
use App\Transformers\Transformer;

class ProposalDetailViewWithHotelTransformer extends Transformer
{
    /**
     * @var DetailedViewTransformer
     */
    private $parentTransformer;

    /**
     * @var HotelTransformer
     */
    private $hotelTransformer;

    /**
     * ProposalDetailViewWithHotelTransformer constructor.
     * @param DetailedViewTransformer $parentTransformer
     * @param HotelSimpleViewTransformer $hotelTransformer
     */
    public function __construct(
        DetailedViewTransformer $parentTransformer,
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

        return (object)(collect($base)->merge([
                'hotel' => $this->hotelTransformer->transform($object->hotel)
            ])
        )->toArray();
    }
}