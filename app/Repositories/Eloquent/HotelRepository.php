<?php

namespace App\Repositories\Eloquent;

use App\Models\Hotel;
use App\Repositories\Contracts\HotelRepositoryInterface;
use DB;

class HotelRepository extends Repository implements HotelRepositoryInterface
{
    /**
     * Attach user to hotel as hotelier
     *
     * @param int $hotelId
     * @param int $userId
     *
     * @return mixed
     */
    public function attachHotelier($hotelId, $userId)
    {
        return $this->find($hotelId)
            ->hoteliers()
            ->attach($userId);
    }

    /**
     * Attach a hotel to proposal request
     *
     * @param int $hotelId
     * @param int $requestId
     *
     * @return mixed
     */
    public function attachToProposalRequest($hotelId, $requestId)
    {
        $hotel = $this->find($hotelId);

        if ($hotel->proposalRequests()->where('proposal_requests.id', $requestId)->count() > 0) {
            return false;
        }

        $hotel
            ->proposalRequests()
            ->attach($requestId);
    }

    /**
     * Detach a hotel from proposal request
     *
     * @param int $hotelId
     * @param int $requestId
     *
     * @return mixed
     */
    public function detachFromProposalRequest($hotelId, $requestId)
    {
        return $this->find($hotelId)
            ->proposalRequests()
            ->detach($requestId);
    }

    /**
     * Get a collection of hotels attached to this proposal request
     *
     * @param int $requestId
     *
     * @return mixed
     */
    public function allAttachedToProposalRequest($requestId)
    {
        return $this->model
            ->join('hotel_proposal_request', 'hotel_proposal_request.hotel_id', '=', 'hotels.id')
            ->join('proposal_requests', 'proposal_requests.id', '=', 'hotel_proposal_request.proposal_request_id')
            ->where('proposal_requests.id', $requestId)
            ->get(['hotels.*']);
    }

    /**
     * Pull any existing property with the same name and similar coordinates
     *
     * @param string $name
     * @param float $latitude
     * @param float $longitude
     *
     * @return mixed
     */
    public function findPropertyMatch($name, $latitude, $longitude)
    {
         return $this->model
             ->whereName($name)
             ->where('latitude', '>=', floor($latitude * 100) / 100)
             ->where('latitude', '<', (floor($latitude * 100) / 100) + 0.01)
             ->where('longitude', '>=', floor($longitude * 100) / 100)
             ->where('longitude', '<', (floor($longitude * 100) / 100) + 0.01)
             ->first();
    }

    /**
     * Find a hotel with a name resembling the passed-in string
     *
     * @param string $name
     *
     * @return mixed
     */
    public function searchByName($name)
    {
        return $this->model
            ->whereRaw('LOWER(name) LIKE "%' . strtolower($name) . '%"')
            ->get();
    }
}
