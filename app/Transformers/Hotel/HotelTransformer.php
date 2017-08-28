<?php

namespace App\Transformers\Hotel;

use App\Models\Amenity;
use App\Transformers\Amenity\AmenityTransformer;
use App\Transformers\HotelImage\HotelImageTransformer;
use App\Transformers\Transformer;

class HotelTransformer extends Transformer
{
    /**
     * @var HotelImageTransformer
     */
    private $imageTransformer;

    /**
     * @var AmenityTransformer
     */
    private $amenityTransformer;

    /**
     * HotelTransformer constructor.
     * @param HotelImageTransformer $imageTransformer
     * @param AmenityTransformer $amenityTransformer
     */
    public function __construct(
        HotelImageTransformer $imageTransformer,
        AmenityTransformer $amenityTransformer
    )
    {
        $this->imageTransformer = $imageTransformer;
        $this->amenityTransformer = $amenityTransformer;
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
            'amenities' => $this->amenityTransformer->transformCollection($object->amenities),
            'google_updated_at' => self::dateFormatOrNull($object->google_updated_at, 'Y-m-d H:i:s'),
        ])->toArray();
    }
}
