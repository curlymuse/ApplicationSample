<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\HotelCorrelationRepositoryInterface;

class HotelCorrelationRepository extends Repository implements HotelCorrelationRepositoryInterface
{
    /**
     * Get an array of Hotels, keyed by correlation ID, based on a specific source
     *
     * @param string $source
     *
     * @return mixed
     */
    public function allHotelsUsingSource($source)
    {
        return collect(
            $this->model
                ->whereSource($source)
                ->get()
                ->keyBy('correlation_id')
        )->map(function($item) {
            return $item->hotel_id;
        });
    }

    /**
     * Find a hotel for this source and correlation ID, if it exists
     *
     * @param string $source
     * @param string $correlationId
     *
     * @return mixed
     */
    public function findHotelForSourceAndCorrelationId($source, $correlationId)
    {
        $correlationItem = $this
            ->findWhere([
                'source'    => $source,
                'correlation_id'    => $correlationId,
            ]);

        if (! $correlationItem) {
            return null;
        }

        return $correlationItem->hotel;
    }
}