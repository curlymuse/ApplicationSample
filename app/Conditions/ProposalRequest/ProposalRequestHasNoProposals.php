<?php

namespace App\Conditions\ProposalRequest;

use App\Conditions\Condition;
use App\Jobs\Job;
use App\Repositories\Contracts\ProposalRequestRepositoryInterface;

class ProposalRequestHasNoProposals extends Condition
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
        return (! $this->proposalRequestRepo->hasProposals($this->job->getRequestId()));
    }
}
