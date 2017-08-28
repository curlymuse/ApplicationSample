<?php

namespace App\Events\Licensee\Clause;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ClauseWasCreated
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var int
     */
    private $licenseeId;

    /**
     * @var array
     */
    private $attributes;

    /**
     * Create a new event instance.
     *
     * @param int $licenseeId
     * @param array $attributes
     */
    public function __construct($licenseeId, $attributes = [])
    {
        $this->licenseeId = $licenseeId;
        $this->attributes = $attributes;
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
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
}
