<?php

namespace App\Events\Licensee\Terms;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class LicenseeTermWasUpdated
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var int
     */
    private $termId;

    /**
     * @var string
     */
    private $newDescription;

    /**
     * Create a new event instance.
     *
     * @param int $termId
     * @param string $newDescription
     */
    public function __construct($termId, $newDescription)
    {
        $this->termId = $termId;
        $this->newDescription = $newDescription;
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

    /**
     * @return string
     */
    public function getNewDescription()
    {
        return $this->newDescription;
    }
}
