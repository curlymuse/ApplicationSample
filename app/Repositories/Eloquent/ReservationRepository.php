<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\ReservationRepositoryInterface;

class ReservationRepository extends Repository implements ReservationRepositoryInterface
{
    /**
     * Get all reservations for this licensee
     *
     * @param int $licenseeId
     *
     * @return mixed
     */
    public function allForLicensee($licenseeId)
    {
        return $this->model
            ->whereHas('roomSets', function($query) use ($licenseeId) {
                $query->whereHas('contract', function($query) use ($licenseeId) {
                    $query->whereHas('proposal', function($query) use ($licenseeId) {
                        $query->whereHas('proposalRequest', function($query) use ($licenseeId) {
                            $query->whereHas('event', function($query) use ($licenseeId) {
                                $query->whereLicenseeId($licenseeId);
                            });
                        });
                    });
                });
            })
            ->get();
    }

    /**
     * Get all reservations for this contract
     *
     * @param int $contractId
     *
     * @return mixed
     */
    public function allForContract($contractId)
    {
        return $this->model
            ->whereHas('roomSets', function($query) use ($contractId) {
                $query->whereContractId($contractId);
            })
            ->get();
    }

    /**
     * Get all reservations for this hotel
     *
     * @param int $hotelId
     * @param int $contractId
     *
     * @return mixed
     */
    public function allForHotelAndContract($hotelId, $contractId)
    {
        return $this->model
            ->whereHas('roomSets', function($query) use ($contractId, $hotelId) {
                $query
                    ->whereContractId($contractId)
                    ->whereHas('contract', function($query) use ($hotelId) {
                        $query->whereHas('proposal', function($query) use ($hotelId) {
                            $query->whereHotelId($hotelId);
                        });
                    });
            })
            ->get();
    }

    /**
     * Sync room sets to reservation, creating room nights
     *
     * @param int $reservationId
     * @param array $roomSetIds
     *
     * @return mixed
     */
    public function syncRoomSets($reservationId, $roomSetIds = [])
    {
        $this->find($reservationId)
            ->roomSets()
            ->sync($roomSetIds);
    }

    /**
     * Sync guests to reservation, including pivot data
     *
     * @param int $reservationId
     * @param array $guests
     *
     * @return mixed
     */
    public function syncGuests($reservationId, $guests = [])
    {
        $formattedGuests = [];
        foreach ($guests as $guest) {
            $formattedGuests[$guest['id']] = collect($guest)->except('id')->toArray();
        };

        $this->find($reservationId)
            ->guests()
            ->sync($formattedGuests);
    }
}