<?php

namespace App\Events\Licensee\ProposalRequest;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class EventDetailsWereUpdated extends Event
{
    use SerializesModels;

    /**
     * @var int
     */
    private $requestId;

    /**
     * @var array
     */
    private $requestAttributes;

    /**
     * @var array
     */
    private $eventAttributes;

    /**
     * Create a new event instance.
     *
     * @param int $requestId
     */
    public function __construct($requestId, $requestAttributes = [], $eventAttributes = [])
    {
        $this->requestId = $requestId;
        $this->requestAttributes = $requestAttributes;
        $this->eventAttributes = $eventAttributes;
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
     * @return int
     */
    public function getRequestId()
    {
        return $this->requestId;
    }

    /**
     * @return array
     */
    public function getRequestAttributes()
    {
        return $this->requestAttributes;
    }

    /**
     * @return array
     */
    public function getEventAttributes()
    {
        return $this->eventAttributes;
    }

}
