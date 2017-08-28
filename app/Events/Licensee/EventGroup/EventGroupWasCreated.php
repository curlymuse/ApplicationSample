<?php

namespace App\Events\Licensee\EventGroup;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class EventGroupWasCreated
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var int
     */
    private $clientId;

    /**
     * @var string
     */
    private $name;

    /**
     * Create a new event instance.
     *
     * @param int $clientId
     * @param string $name
     */
    public function __construct($clientId, $name)
    {
        $this->clientId = $clientId;
        $this->name = $name;
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
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
