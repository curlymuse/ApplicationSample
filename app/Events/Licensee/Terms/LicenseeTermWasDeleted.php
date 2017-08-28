<?php

namespace App\Events\Licensee\Terms;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class LicenseeTermWasDeleted
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var int
     */
    private $termId;

    /**
     * Create a new event instance.
     *
     * @param int $termId
     */
    public function __construct($termId)
    {
        $this->termId = $termId;
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
    public function getTermId()
    {
        return $this->termId;
    }
}
