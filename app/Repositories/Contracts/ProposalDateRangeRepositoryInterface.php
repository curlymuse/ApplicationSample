<?php

namespace App\Repositories\Contracts;

interface ProposalDateRangeRepositoryInterface
{
    /**
     * Get all submitted Proposals, represent by ProposalDateRange, for this PR
     *
     * @param int $proposalRequestId
     *
     * @return mixed
     */
    public function allSubmittedForRequest($proposalRequestId);

    /**
     * Proposal has this date range attached
     *
     * @param int $eventDateRangeId
     * @param int $proposalId
     *
     * @return mixed
     */
    public function dateRangeExistsOnProposal($eventDateRangeId, $proposalId);
}