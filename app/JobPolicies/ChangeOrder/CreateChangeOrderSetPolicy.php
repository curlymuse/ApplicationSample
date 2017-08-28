<?php

namespace App\JobPolicies\ChangeOrder;

use App\Conditions\Contract\ContractHasNoPendingChangeOrders;
use App\Exceptions\JobPolicy\InvalidStateException;
use App\JobPolicies\JobPolicy;

class CreateChangeOrderSetPolicy extends JobPolicy
{
    /**
     * An array of Conditions
     *
     * @var array<Condition>
     */
    protected static $conditions = [
        ContractHasNoPendingChangeOrders::class         => InvalidStateException::class,
    ];
}