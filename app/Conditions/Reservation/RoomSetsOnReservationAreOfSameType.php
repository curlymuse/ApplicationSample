<?php

namespace App\Conditions\Reservation;

use App\Conditions\Condition;
use App\Jobs\Job;
use App\Repositories\Contracts\RoomSetRepositoryInterface;

class RoomSetsOnReservationAreOfSameType extends Condition
{
    /**
     * @var Job
     */
    protected $job;

    /**
     * @var \App\Repositories\Contracts\RoomSetRepositoryInterface
     */
    private $roomSetRepo;

    /**
     * @param Job $job
     */
    public function __construct(Job $job)
    {
        $this->job = $job;

        $this->roomSetRepo = app(RoomSetRepositoryInterface::class);
    }

    /**
     * Determine whether the job follows the condition
     *
     * @return boolean
     */
    public function holds()
    {
        if ($this->job->getRoomSetIds() == null) {
            return true;
        }

        return $this->roomSetRepo->haveSameName(
            $this->job->getRoomSetIds()
        );
    }
}