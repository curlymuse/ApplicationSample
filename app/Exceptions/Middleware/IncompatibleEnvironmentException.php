<?php

namespace App\Exceptions\Middleware;

class IncompatibleEnvironmentException extends MiddlewareException
{
    /**
     * @var int
     */
    protected static $statusCode = 403;
}
