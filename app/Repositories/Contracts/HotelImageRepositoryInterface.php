<?php

namespace App\Repositories\Contracts;

interface HotelImageRepositoryInterface
{
    /**
     * Store image for hotel
     *
     * @param int $hotelId
     * @param string $mainUrl
     * @param string|null $thumbnailUrl
     *
     * @return mixed
     */
    public function storeForHotel($hotelId, $mainUrl, $thumbnailUrl = null);
}