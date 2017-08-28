<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\ProposalDateRangeRepositoryInterface;

class ProposalDateRangeRepository extends Repository implements ProposalDateRangeRepositoryInterface
{
    /**
     * Get all submitted Proposals, represent by ProposalDateRange, for this PR
     *
     * @param int $proposalRequestId
     *
     * @return mixed
     */
    public function allSubmittedForRequest($proposalRequestId)
    {
        return $this->model
            ->whereNotNull('submitted_at')
            ->whereNotNull('submitted_by_user')
            ->whereHas('proposal', function($query) use ($proposalRequestId) {
                $query->whereProposalRequestId($proposalRequestId);
            })->get();
    }

    /**
     * Proposal has this date range attached
     *
     * @param int $eventDateRangeId
     * @param int $proposalId
     *
     * @return mixed
     */
    public function dateRangeExistsOnProposal($eventDateRangeId, $proposalId)
    {
        return $this->model
            ->whereProposalId($proposalId)
            ->whereEventDateRangeId($eventDateRangeId)
            ->exists();
    }
}