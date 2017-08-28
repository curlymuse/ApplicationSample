<?php

namespace App\Events\ChangeOrder;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ChangeOrderItemWasDeclined
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var int
     */
    private $changeOrderId;

    /**
     * @var int
     */
    private $userId;

    /**
     * @var string
     */
    private $reason;

    /**
     * Create a new event instance.
     *
     * @param int $changeOrderId
     * @param int $userId
     * @param string $reason
     */
    public function __construct($changeOrderId, $userId, $reason)
    {
        $this->changeOrderId = $changeOrderId;
        $this->userId = $userId;
        $this->reason = $reason;
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
    public function getChangeOrderId()
    {
        return $this->changeOrderId;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }
}
