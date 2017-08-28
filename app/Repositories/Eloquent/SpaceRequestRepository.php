<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\SpaceRequestRepositoryInterface;

class SpaceRequestRepository extends Repository implements SpaceRequestRepositoryInterface
{
    /**
     * Sync space requests for event date range
     *
     * @param int $proposalRequestId
     * @param int $eventDateRangeId
     * @param array $spaces
     * @param string $type
     *
     * @return mixed
     */
    public function syncForEventDateRange($proposalRequestId, $eventDateRangeId, $spaces = [], $type = 'Meeting')
    {
        $this->model
            ->whereType($type)
            ->whereProposalRequestId($proposalRequestId)
            ->whereEventDateRangeId($eventDateRangeId)
            ->delete();

        foreach ($spaces as $data) {
            $data['type'] = $type;
            $data['event_date_range_id'] = $eventDateRangeId;
            $data['proposal_request_id'] = $proposalRequestId;
            $this->store($data);
        }
    }
}
