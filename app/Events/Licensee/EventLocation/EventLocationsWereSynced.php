<?php

namespace App\Events\Licensee\EventLocation;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class EventLocationsWereSynced
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var int
     */
    private $requestId;

    /**
     * @var array
     */
    private $locationIds;

    /**
     * Create a new event instance.
     *
     * @param int $requestId
     * @param array $locationIds
     */
    public function __construct($requestId, $locationIds = [])
    {
        $this->requestId = $requestId;
        $this->locationIds = $locationIds;
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
    public function getRequestId()
    {
        return $this->requestId;
    }

    /**
     * @return array
     */
    public function getLocationIds()
    {
        return $this->locationIds;
    }
}
