<?php

namespace App\Conditions\ProposalRequest;

use App\Conditions\Condition;
use App\Jobs\Job;
use App\Repositories\Contracts\ProposalRequestRepositoryInterface;

class ProposalRequestIsWellFormed extends Condition
{
    /**
     * @var Job
     */
    protected $job;

    /**
     * @var \App\Repositories\Contracts\ProposalRequestRepositoryInterface
     */
    private $proposalRequestRepo;

    /**
     * @param Job $job
     */
    public function __construct(Job $job)
    {
        $this->job = $job;

        $this->proposalRequestRepo = app(ProposalRequestRepositoryInterface::class);
    }

    /**
     * Determine whether the job follows the condition
     *
     * @return boolean
     */
    public function holds()
    {
        $proposalRequest = $this->proposalRequestRepo->find($this->job->getRequestId());

        //  Make sure there is at least one event location
        $locations = $proposalRequest->eventLocations;
        if (! $locations || count($locations) == 0) {
            return false;
        }

        $dateRanges = $proposalRequest->event->dateRanges;

        //  Make sure there is at least one date range
        if (! $dateRanges || count($dateRanges) == 0) {
            return false;
        }

        //  Check that every date range has at least one room request
        foreach ($dateRanges as $dateRange) {
            if ($dateRange->roomRequestDates()->count() == 0) {
                return false;
            }
        }

        return true;
    }
}
