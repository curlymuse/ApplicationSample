<?php

namespace App\Conditions\EventDateRange;

use App\Conditions\Condition;
use App\Jobs\Job;
use App\Repositories\Contracts\EventDateRangeRepositoryInterface;

class EventDateRangeDoesNotExistOnThisProposalRequest extends Condition
{
    /**
     * @var Job
     */
    protected $job;

    /**
     * @var \App\Repositories\Contracts\EventDateRangeRepositoryInterface
     */
    private $eventDateRangeRepo;

    /**
     * @param Job $job
     */
    public function __construct(Job $job)
    {
        $this->job = $job;
        $this->eventDateRangeRepo = app(EventDateRangeRepositoryInterface::class);
    }

    /**
     * Determine whether the job follows the condition
     *
     * @return boolean
     */
    public function holds()
    {
        return ! $this->eventDateRangeRepo->existsForProposalRequest(
            $this->job->getRequestId(),
            $this->job->getAttributes()
        );
    }
}