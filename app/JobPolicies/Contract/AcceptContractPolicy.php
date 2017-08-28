<?php

namespace App\JobPolicies\Contract;

use App\Conditions\Contract\ContractHasChangeOrders;
use App\Conditions\Contract\ContractIsNotOffline;
use App\Conditions\Contract\ContractOwnerActionMatchesUser;
use App\Conditions\Contract\LatestChangeOrderIsFullyAccepted;
use App\Exceptions\JobPolicy\InvalidStateException;
use App\Exceptions\JobPolicy\UserActionForbiddenException;
use App\JobPolicies\JobPolicy;

class AcceptContractPolicy extends JobPolicy
{
    /**
     * An array of Conditions
     *
     * @var array<Condition>
     */
    protected static $conditions = [
        ContractIsNotOffline::class         => InvalidStateException::class,
        ContractHasChangeOrders::class      => InvalidStateException::class,
        LatestChangeOrderIsFullyAccepted::class      => InvalidStateException::class,
        ContractOwnerActionMatchesUser::class       => UserActionForbiddenException::class,
    ];
}