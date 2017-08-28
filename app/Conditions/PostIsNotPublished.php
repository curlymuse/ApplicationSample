<?php

namespace App\Conditions;

use App\Conditions\Condition;
use App\Jobs\Job;

class PostIsNotPublished extends Condition
{
    /**
     * @var Job
     */
    protected $job;

    /**
     * @param Job $job
     */
    public function __construct(Job $job)
    {
        $this->job = $job;
    }

    /**
     * Determine whether the job follows the condition
     *
     * @return boolean
     */
    public function holds()
    {
        return true;
    }
}
