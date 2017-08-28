<?php

namespace App\Events\User;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserWasDetachedFromPlanner
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var int
     */
    private $userId;

    /**
     * @var int
     */
    private $plannerId;

    /**
     * Create a new event instance.
     *
     * @param int $userId
     * @param int $plannerId
     */
    public function __construct($userId, $plannerId)
    {
        $this->userId = $userId;
        $this->plannerId = $plannerId;
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
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return int
     */
    public function getPlannerId()
    {
        return $this->plannerId;
    }
}
