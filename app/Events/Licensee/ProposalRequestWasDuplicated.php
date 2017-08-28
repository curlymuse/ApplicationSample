<?php

namespace App\Events\Licensee;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ProposalRequestWasDuplicated
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var int
     */
    private $requestId;

    /**
     * @var string
     */
    private $newName;

    /**
     * Create a new event instance.
     *
     * @param int $requestId
     * @param string $newName
     */
    public function __construct($requestId, $newName)
    {
        $this->requestId = $requestId;
        $this->newName = $newName;
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
     * @return string
     */
    public function getNewName()
    {
        return $this->newName;
    }
}
