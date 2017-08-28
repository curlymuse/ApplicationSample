<?php

namespace App\Exceptions;

class MyException extends \Exception
{
    /**
     * @var int
     */
    protected static $statusCode = 422;

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return static::$statusCode;
    }
}
