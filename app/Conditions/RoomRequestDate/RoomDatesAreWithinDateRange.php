<?php

namespace App\Conditions\RoomRequestDate;

use App\Conditions\Condition;
use App\Jobs\Job;
use App\Repositories\Contracts\EventDateRangeRepositoryInterface;
use Carbon\Carbon;

class RoomDatesAreWithinDateRange extends Condition
{
    /**
     * @var Job
     */
    protected $job;
    
    /**
     * @var \App\Repositories\Contracts\EventDateRangeRepositoryInterface
     */
    private $dateRangeRepo;

    /**
     * @param Job $job
     */
    public function __construct(Job $job)
    {
        $this->job = $job;
        $this->dateRangeRepo = app(EventDateRangeRepositoryInterface::class);
    }

    /**
     * Determine whether the job follows the condition
     *
     * @return boolean
     */
    public function holds()
    {
        $dateRange = $this->dateRangeRepo->find($this->job->getEventDateRangeId());

        foreach ($this->job->getRoomRequestDates() as $roomType) {
            foreach ($roomType['room_nights'] as $night) {
                if ($dateRange->start_date->gt(new Carbon($night['date']))) {
                    return false;
                }
                if ($dateRange->end_date->lt(new Carbon($night['date']))) {
                    return false;
                }
            }
        }

        return true;
    }
}