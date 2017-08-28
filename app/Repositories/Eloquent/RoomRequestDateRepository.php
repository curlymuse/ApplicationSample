<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\RoomRequestDateRepositoryInterface;
use App\Repositories\Eloquent\Repository;

class RoomRequestDateRepository extends Repository implements RoomRequestDateRepositoryInterface
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
    public function syncForEventDateRange($proposalRequestId, $eventDateRangeId, $roomRequestDates = [])
    {
        $this->model
            ->whereProposalRequestId($proposalRequestId)
            ->whereEventDateRangeId($eventDateRangeId)
            ->delete();

        foreach ($roomRequestDates as $set) {
            foreach ($set['room_nights'] as $night) {
                $this->model->create([
                    'event_date_range_id' => $eventDateRangeId,
                    'proposal_request_id' => $proposalRequestId,
                    'rooms_requested' => $night['rooms'],
                    'room_date' => $night['date'],
                    'room_type_name' => $set['name'],
                    'preferred_rate_min' => $set['preferred_rate_min'],
                    'preferred_rate_max' => $set['preferred_rate_max'],
                ]);
            }
        }
    }
}
