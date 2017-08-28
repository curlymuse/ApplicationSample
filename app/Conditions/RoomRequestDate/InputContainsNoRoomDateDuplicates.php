<?php

namespace App\Conditions\RoomRequestDate;

use App\Conditions\Condition;
use App\Jobs\Job;

class InputContainsNoRoomDateDuplicates extends Condition
{
    /**
     * @var Job
     */
    protected $job;

    /**
     * @param Job $job
     */
    public function __construct(Job $job)
    {
        $this->job = $job;
    }

    /**
     * Determine whether the job follows the condition
     *
     * @return boolean
     */
    public function holds()
    {
        $entries = collect([]);

        foreach ($this->job->getRoomRequestDates() as $roomType) {
            foreach ($roomType['room_nights'] as $night) {
                $key = sprintf(
                    '%s%s',
                    $roomType['name'],
                    $night['date']
                );
                if ($entries->contains($key)) {
                    return false;
                }
                $entries->push($key);
            }
        }

        return true;
    }
}