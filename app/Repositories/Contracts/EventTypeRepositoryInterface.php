<?php

namespace App\Repositories\Contracts;

interface EventTypeRepositoryInterface
{
    /**
     * Get a list of all events with their subtypes
     *
     * @return mixed
     */
    public function allWithSubTypes();
}