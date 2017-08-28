<?php

namespace App\Exceptions\JobPolicy;

use App\Exceptions\MyException;

abstract class JobPolicyException extends MyException
{

    /**
     * @var int
     */
    protected static $statusCode = 422;
}
