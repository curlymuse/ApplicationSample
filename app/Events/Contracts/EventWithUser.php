<?php

namespace App\Events\Contracts;

interface EventWithUser
{
    /**
     * Return the User ID
     *
     * @return int
     */
    public function getUserId();
}