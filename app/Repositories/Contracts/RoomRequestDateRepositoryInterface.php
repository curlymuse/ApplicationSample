<?php

namespace App\Repositories\Contracts;

interface RoomRequestDateRepositoryInterface
{
    /**
     * Sync the set of RoomRequestDate objects in the database for the given
     * EventDateRange with the set of date/room combinations provided
     *
     * $roomRequestDates should have the form [[rooms => ##, date => YYYY-MM-DD], ...]
     *
     * @param int $proposalRequestId
     * @param int $eventDateRangeId
     * @param array $roomRequestDates
     *
     * @return mixed
     */
    public function syncForEventDateRange($proposalRequestId, $eventDateRangeId, $roomRequestDates = []);
}
