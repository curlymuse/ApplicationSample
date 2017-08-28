<?php

namespace App\Repositories\Contracts;

interface HotelCorrelationRepositoryInterface
{
    /**
     * Get an array of Hotels, keyed by correlation ID, based on a specific source
     *
     * @param string $source
     *
     * @return mixed
     */
    public function allHotelsUsingSource($source);

    /**
     * Find a hotel for this source and correlation ID, if it exists
     *
     * @param string $source
     * @param string $correlationId
     *
     * @return mixed
     */
    public function findHotelForSourceAndCorrelationId($source, $correlationId);
}