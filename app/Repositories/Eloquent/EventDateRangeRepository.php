<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\EventDateRangeRepositoryInterface;

class EventDateRangeRepository extends Repository implements EventDateRangeRepositoryInterface
{
    /**
     * Get all EventDateRange objects for this ProposalRequest
     *
     * @param int $requestId
     *
     * @return mixed
     */
    public function allForProposalRequest($requestId)
    {
        return $this->model
            ->whereHas('event', function($query) use ($requestId)
            {
                $query->whereHas('proposalRequests', function($query) use ($requestId) {
                    $query->where('proposal_requests.id', $requestId);
                });
            })
            ->orderBy('start_date', 'asc')
            ->get();
    }

    /**
     * Create a new event date range for the event
     *
     * @param int $eventId
     * @param $attributes
     *
     * @return mixed
     */
    public function storeForEvent($eventId, $attributes)
    {
        $data = $attributes;
        $data['event_id'] = $eventId;

        return $this->model->create($data)->id;
    }

    /**
     * Does an EventDateRange with these attributes exist on this Proposal Request?
     *
     * @param int $requestId
     * @param array $attributes
     *
     * @return mixed
     */
    public function existsForProposalRequest($requestId, $attributes = [])
    {
        return $this->model
            ->whereHas('event', function($query) use ($requestId)
            {
                $query->whereHas('proposalRequests', function($query) use ($requestId)
                {
                    $query->where('proposal_requests.id', $requestId);
                });
            })
            ->where($attributes)
            ->exists();
    }
}
