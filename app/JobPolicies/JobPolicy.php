<?php

namespace App\JobPolicies;

use App\Jobs\Job;
use Exception;

abstract class JobPolicy
{

    /**
     * An array of Conditions
     *
     * @var array<Condition>
     */
    protected static $conditions = [];

    /**
     * Check whether each condition in the policy holds
     *
     * @param Job $job
     * @throws Exception
     * @return bool
     */
    public function complies(Job $job)
    {
        collect(static::$conditions)->each(function ($exceptionClass, $conditionClass) use ($job) {
        //  Instantiate the condition
            $condition = new $conditionClass($job);

            //  If condition holds, we're done
            if ($condition->holds()) {
                return true;
            }

            //  Grab error message from lang
            $policyIndex = class_basename($this);
            $conditionIndex = class_basename($condition);
            $errorText = trans(sprintf('policies/%s.%s', $policyIndex, $conditionIndex));

            //  Throw the appropriate Exception
            throw new $exceptionClass($errorText);
        });

        //  If we're this far, all conditions hold, and therefore the policy is in compliance
        return true;
    }
}
