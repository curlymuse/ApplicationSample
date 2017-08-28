<?php

namespace App\Repositories\Contracts;

interface GuestRepositoryInterface
{
    /**
     * Add an existing guest to a reservation
     *
     * @param int $guestId
     * @param int $reservationId
     * @param array $attributes
     *
     * @return mixed
     */
    public function addGuestToReservation($guestId, $reservationId, $attributes = []);
}