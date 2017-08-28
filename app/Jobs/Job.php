<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;

abstract class Job
{
    /*
    |--------------------------------------------------------------------------
    | Queueable Jobs
    |--------------------------------------------------------------------------
    |
    | This job base class provides a central location to place any logic that
    | is shared across all of your jobs. The trait included with the class
    | provides access to the "onQueue" and "delay" queue helper methods.
    |
    */

    use Queueable;

    /**
     * Verify whether the job complies with its respective policy
     *
     * @throws Exception
     *
     * @return boolean
     */
    protected function verifyCompliance()
    {
        //  Get the policy based on the job name
        $policyClass = sprintf('%sPolicy', str_replace('Jobs', 'JobPolicies', get_class($this)));

        //  Job complies if no policy exists
        if (! class_exists($policyClass)) {
            return true;
        }

        //  Instantiate the policy
        $policy = app($policyClass);

        //  Policy will either return true or throw an exception
        return $policy->complies($this);
    }
}
