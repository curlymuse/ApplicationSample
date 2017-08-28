<?php

namespace App\Events\Licensee\Event;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class EventWasCreated
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var int
     */
    private $licenseeId;

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
     * @param int $licenseeId
     * @param int $clientId
     * @param string $name
     */
    public function __construct($licenseeId, $clientId, $name)
    {
        $this->licenseeId = $licenseeId;
        $this->name = $name;
        $this->clientId = $clientId;
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
    public function getLicenseeId()
    {
        return $this->licenseeId;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getClientId()
    {
        return $this->clientId;
    }
}
