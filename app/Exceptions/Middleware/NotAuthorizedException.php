<?php

namespace App\Exceptions\Middleware;

class NotAuthorizedException extends MiddlewareException
{
    /**
     * @var int
     */
    protected static $statusCode = 403;
}
