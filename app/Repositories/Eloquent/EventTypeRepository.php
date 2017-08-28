<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\EventTypeRepositoryInterface;

class EventTypeRepository extends Repository implements EventTypeRepositoryInterface
{
    /**
     * Get a list of all events with their subtypes
     *
     * @return mixed
     */
    public function allWithSubTypes()
    {
        return $this->model
            ->whereNull('parent_id')
            ->with('children')
            ->orderBy('name', 'asc')
            ->get();
    }
}