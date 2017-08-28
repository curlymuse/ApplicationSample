<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\HotelImageRepositoryInterface;

class HotelImageRepository extends Repository implements HotelImageRepositoryInterface
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
    public function storeForHotel($hotelId, $mainUrl, $thumbnailUrl = null)
    {
        return $this->store([
            'hotel_id'  => $hotelId,
            'main_path' => $mainUrl,
            'source_path'   => $mainUrl,
            'thumbnail_path'    => $thumbnailUrl,
        ]);
    }
}