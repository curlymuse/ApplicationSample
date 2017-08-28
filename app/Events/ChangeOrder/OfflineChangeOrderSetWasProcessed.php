<?php

namespace App\Events\ChangeOrder;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class OfflineChangeOrderSetWasProcessed
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var int
     */
    private $changeOrderSetId;

    /**
     * Create a new event instance.
     *
     * @param int $changeOrderSetId
     */
    public function __construct($changeOrderSetId)
    {
        $this->changeOrderSetId = $changeOrderSetId;
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
    public function getChangeOrderSetId()
    {
        return $this->changeOrderSetId;
    }
}
