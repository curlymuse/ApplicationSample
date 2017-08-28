<?php

namespace App\Events\Licensee\Clause;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ClauseWasDeleted
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var int
     */
    private $clauseId;

    /**
     * Create a new event instance.
     *
     * @param int $clauseId
     */
    public function __construct($clauseId)
    {
        $this->clauseId = $clauseId;
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
    public function getClauseId()
    {
        return $this->clauseId;
    }
}
