<?php

namespace App\Transformers\Hotel;

use App\Transformers\HotelImage\HotelImageTransformer;
use App\Transformers\Transformer;

class HotelSearchResultTransformer extends Transformer
{
    /**
     * @var HotelImageTransformer
     */
    private $imageTransformer;

    /**
     * HotelSearchResultTransformer constructor.
     * @param HotelImageTransformer $imageTransformer
     */
    public function __construct(HotelImageTransformer $imageTransformer)
    {
        $this->imageTransformer = $imageTransformer;
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
            'brand_id',
            'name',
            'city',
            'zip',
            'state',
            'country',
            'latitude',
            'longitude',
            'description',
            'sleeping_rooms',
            'meeting_rooms',
            'largest_meeting_room_sq_ft',
            'total_meeting_room_sq_ft',
            'rate_min',
            'rate_max',
            'travelocity_stars',
            'travelocity_rating',
            'travelocity_reviews',
            'mobil_star_rating',
            'place_id',
            'google_stars',
            'google_latitude',
            'google_longitude',
        ])->merge([
            'address'   => $object->address1,
            'property_type' => data_get($object, 'propertyType.name'),
            'images'    => $this->imageTransformer->transformCollection($object->images),
            'google_updated_at' => self::dateFormatOrNull($object->google_updated_at, 'Y-m-d H:i:s'),
        ])->toArray();
    }
}