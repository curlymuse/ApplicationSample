<?php

namespace App\Conditions;

use Exception;

abstract class Condition
{
    /**
     * The job against which the condition is checked
     *
     * @var \App\Jobs\Job
     */
    protected $job;

    /**
     * Determine whether the job follows the condition
     *
     * @throws Exception
     * @return boolean
     */
    abstract public function holds();
}
