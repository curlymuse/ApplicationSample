<?php

namespace App\Repositories\Eloquent;

use Illuminate\Support\Facades\Log;
use App\Repositories\Contracts\EventLocationRepositoryInterface;
use App\Repositories\Contracts\HotelSearchRepositoryInterface;

class HotelSearchRepository extends Repository implements HotelSearchRepositoryInterface
{

    /**
     * Search terms
     *
     * @var array
     */
    private $terms = [];

    /**
     * @var int
     */
    private $resultLimit;

    /**
     * @var
     */
    private $preQueryModel;

    /**
     * @var \App\Repositories\Contracts\EventLocationRepositoryInterface
     */
    private $locationRepo;

    /**
     * @var \App\Repositories\Contracts\BrandRepositoryInterface
     */
    private $brandRepo;

    /**
     * @param $model
     * @param $locationRepo
     * @param $brandRepo
     */
    public function __construct($model, $locationRepo, $brandRepo)
    {
        parent::__construct($model);

        $this->resultLimit = config('resbeat.misc.hotel-search-results-limit');

        $this->locationRepo = $locationRepo;
        $this->brandRepo = $brandRepo;
    }

    /**
     * Return a list of hotels matching these criteria
     *
     * @param array $terms
     *
     * @return mixed
     */
    public function search($terms = [])
    {
        $this->terms = $terms;
        $this->preQueryModel = $this->model;

        return $this
            ->withValidLatitudeLongitude()
            ->withMinSleepingRooms($this->term('min_sleeping_rooms'))
            ->withMaxSleepingRooms($this->term('max_sleeping_rooms'))
            ->withMinMeetingRooms($this->term('min_meeting_rooms'))
            ->withMinLargestMeetingRoomSqFt($this->term('min_largest_meeting_room_sq_ft'))
            ->withStarsMatching($this->term('stars'))
            ->withBrands($this->term('brands'))
            ->withPropertyTypes($this->term('types'))
            ->nearLocations($this->term('locations'), $this->term('radius'), $this->term('radius_units'))
            ->complete();
    }

    /**
     * Exclude hotels with no lat/long
     *
     * @return $this
     */
    private function withValidLatitudeLongitude()
    {
        $this->model = $this->model->where(function ($query) {
            $query->where('latitude', '!=', 0)
                ->orWhere('longitude', '!=', 0);
        })->where(function ($query) {
            $query->whereNotNull('latitude')
                ->whereNotNull('longitude');
        });

        return $this;
    }

    /**
     * Only hotels within latitude and longitude of these locations
     *
     * @param array $locations
     * @param float $radius
     * @param string $radiusUnits
     *
     * @return $this
     */
    private function nearLocations($locations = [], $radius = null, $radiusUnits = null)
    {
        if (count($locations) == 0 || $radius === null || !$radiusUnits) {
            return $this;
        }

        $hotels = $this->model->select('id', 'latitude', 'longitude')->get();

        $locationObjects = $this->locationRepo->getWherePlaceIdIn($locations);
        $matchingIds = [];
        $radius = (float) $radius;

        foreach ($locationObjects as $location) {
            //  We need an actual location with actual latitude and longitude, otherwise this is pointless
            if ($location->latitude === null || $location->longitude === null) {
                continue;
            }

            foreach ($hotels as $hotel) {
                if (calculateDistanceUsingCoordinates(
                    $location->latitude,
                    $location->longitude,
                    $hotel->latitude,
                    $hotel->longitude,
                    $radiusUnits
                ) <= $radius) {
                    $matchingIds[] = $hotel->id;
                }
            }
        }

        //  Once we have the matching ID set, we don't need the rest of the query params, so
        //  start from scratch
        $this->model = $this->preQueryModel->whereIn('id', $matchingIds);

        return $this;
    }

    /**
     * @param array $brands
     *
     * @return $this
     */
    private function withBrands($brands = [])
    {
        if (! $brands || empty($brands)) {
            return $this;
        }

        $whitelist = $this->brandRepo->getWhitelist($brands);
        $this->model = $this->model->whereIn('brand_id', $whitelist);

        return $this;
    }

    /**
     * @param array $types
     *
     * @return $this
     */
    private function withPropertyTypes($types = [])
    {
        if (! $types || empty($types)) {
            $types = config('default_property_type_ids');
        }

        $this->model = $this->model->whereIn('property_type_id', $types);

        return $this;
    }

    /**
     * Minimum sleeping rooms
     *
     * @param int $rooms
     *
     * @return $this
     */
    private function withMinSleepingRooms($rooms)
    {
        if (! $rooms) {
            return $this;
        }

        $this->model = $this->model->where('sleeping_rooms', '>=', $rooms);

        return $this;
    }

    /**
     * Maximum sleeping rooms
     *
     * @param int $rooms
     *
     * @return $this
     */
    private function withMaxSleepingRooms($rooms)
    {
        if (! $rooms) {
            return $this;
        }

        $this->model = $this->model->where('sleeping_rooms', '<=', $rooms);

        return $this;
    }

    /**
     * Minimum meeting rooms
     *
     * @param int $rooms
     *
     * @return $this
     */
    private function withMinMeetingRooms($rooms)
    {
        if (! $rooms) {
            return $this;
        }

        $this->model = $this->model->where('meeting_rooms', '>=', $rooms);

        return $this;
    }

    /**
     * Minimum sleeping rooms
     *
     * @param int $rooms
     *
     * @return $this
     */
    private function withMinLargestMeetingRoomSqFt($rooms)
    {
        if (! $rooms) {
            return $this;
        }

        $this->model = $this->model->where('largest_meeting_room_sq_ft', '>=', $rooms);

        return $this;
    }

    /**
     * Filter by star value in list
     *
     * @param string $stars
     *
     * @return $this
     */
    private function withStarsMatching($stars)
    {
        if (! $stars) {
            return $this;
        }

        //  If we have an array, do >= smallest, otherwise >= value
        $starsArray = explode(',', $stars);
        $reference = (count($starsArray) > 1) ? min($starsArray) : $starsArray[0];

        $this->model = $this->model->where(function ($query) use ($reference) {
            $query->where('google_stars', '>=', $reference)
                ->orWhereNull('google_updated_at');
        });

        return $this;
    }

    /**
     * @return mixed
     */
    private function complete()
    {
        return $this->model
            ->with(['propertyType', 'images'])
            ->take($this->resultLimit)
            ->get();
    }

    /**
     * Conditionally pull a term from the term list
     *
     * @param string $key
     *
     * @return null
     */
    private function term($key)
    {
        return (isset($this->terms[$key])) ? $this->terms[$key] : null;
    }
}
