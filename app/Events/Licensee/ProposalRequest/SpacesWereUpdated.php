<?php

namespace App\Events\Licensee\ProposalRequest;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SpacesWereUpdated extends Event
{
    use SerializesModels;

    /**
     * @var int
     */
    private $eventDateRangeId;

    /**
     * @var array
     */
    private $spaces;

    /**
     * Create a new event instance.
     *
     * @param int $eventDateRangeId
     * @param array $spaces
     */
    public function __construct($eventDateRangeId, $spaces = [])
    {
        $this->eventDateRangeId = $eventDateRangeId;
        $this->spaces = $spaces;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }

    /**
     * @return array
     */
    public function getSpaces()
    {
        return $this->spaces;
    }

    /**
     * @return int
     */
    public function getEventDateRangeId()
    {
        return $this->eventDateRangeId;
    }
}
