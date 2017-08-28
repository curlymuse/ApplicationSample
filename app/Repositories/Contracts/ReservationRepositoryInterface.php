<?php

namespace App\Repositories\Contracts;

interface ReservationRepositoryInterface
{
    /**
     * Get all reservations for this licensee
     *
     * @param int $licenseeId
     *
     * @return mixed
     */
    public function allForLicensee($licenseeId);

    /**
     * Get all reservations for this contract
     *
     * @param int $contractId
     *
     * @return mixed
     */
    public function allForContract($contractId);

    /**
     * Get all reservations for this hotel
     *
     * @param int $hotelId
     * @param int $contractId
     *
     * @return mixed
     */
    public function allForHotelAndContract($hotelId, $contractId);

    /**
     * Sync room sets to reservation, creating room nights
     *
     * @param int $reservationId
     * @param array $roomSetIds
     *
     * @return mixed
     */
    public function syncRoomSets($reservationId, $roomSetIds = []);

    /**
     * Sync guests to reservation, including pivot data
     *
     * @param int $reservationId
     * @param array $guests
     *
     * @return mixed
     */
    public function syncGuests($reservationId, $guests = []);
}