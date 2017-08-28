<?php

namespace App\Conditions\EventDateRange;

use App\Conditions\Condition;
use App\Repositories\Contracts\ContractRepositoryInterface;
use App\Jobs\Job;

class DateRangeIsNewOrSelectedForProposalRequest extends Condition
{
    /**
     * @var Job
     */
    protected $job;

    /**
     * @var \App\Repositories\Contracts\ContractRepositoryInterface
     */
    private $contractRepo;

    /**
     * @param Job $job
     */
    public function __construct(Job $job)
    {
        $this->job = $job;
        $this->contractRepo = app(ContractRepositoryInterface::class);
    }

    /**
     * Determine whether the job follows the condition
     *
     * @return boolean
     */
    public function holds()
    {
        $requestId = $this->job->getProposal()->proposal_request_id;
        $contracts = $this->contractRepo->allForProposalRequest($requestId);

        if (count($contracts) == 0) {
            return true;
        }

        foreach ($contracts as $contract) {
            if ($contract->event_date_range_id != $this->job->getEventDateRangeId()) {
                return false;
            }
        }

        return true;
    }
}