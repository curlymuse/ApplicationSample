<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\GuestRepositoryInterface;

class GuestRepository extends Repository implements GuestRepositoryInterface
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
    public function addGuestToReservation($guestId, $reservationId, $attributes = [])
    {
        $this->find($guestId)
            ->reservations()
            ->attach(
                $reservationId,
                $attributes
            );
    }
}