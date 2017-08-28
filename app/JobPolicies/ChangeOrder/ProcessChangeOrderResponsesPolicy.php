<?php

namespace App\JobPolicies\ChangeOrder;

use App\Conditions\Contract\ContractOwnerActionMatchesUser;
use App\Exceptions\JobPolicy\UserActionForbiddenException;
use App\JobPolicies\JobPolicy;

class ProcessChangeOrderResponsesPolicy extends JobPolicy
{
    /**
     * An array of Conditions
     *
     * @var array<Condition>
     */
    protected static $conditions = [
        ContractOwnerActionMatchesUser::class       => UserActionForbiddenException::class,
    ];
}