<?php

namespace App\Events\Licensee\ProposalRequest;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RoomRequestDatesWereUpdated
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var int
     */
    private $eventDateRangeId;

    /**
     * @var array
     */
    private $roomRequestDates;

    /**
     * Create a new event instance.
     *
     * @param int $eventDateRangeId
     * @param array $roomRequestDates
     */
    public function __construct($eventDateRangeId, $roomRequestDates = [])
    {
        $this->eventDateRangeId = $eventDateRangeId;
        $this->roomRequestDates = $roomRequestDates;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }

    /**
     * @return int
     */
    public function getEventDateRangeId()
    {
        return $this->eventDateRangeId;
    }

    /**
     * @return array
     */
    public function getRoomRequestDates()
    {
        return $this->roomRequestDates;
    }
}
