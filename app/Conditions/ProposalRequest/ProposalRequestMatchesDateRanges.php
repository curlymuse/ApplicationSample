<?php

namespace App\Conditions\ProposalRequest;

use App\Conditions\Condition;
use App\Jobs\Job;
use App\Repositories\Contracts\ProposalRequestRepositoryInterface;

class ProposalRequestMatchesDateRanges extends Condition
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
        $request = $this->proposalRequestRepo->find(
            $this->job->getRequestId()
        );

        foreach ($this->job->getAccommodations()['date_ranges'] as $range) {
            if (! $request->event->dateRanges()->pluck('id')->contains($range['event_date_range_id'])) {
                return false;
            }
        }

        return true;
    }
}