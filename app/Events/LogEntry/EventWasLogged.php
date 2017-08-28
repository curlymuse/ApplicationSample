<?php

namespace App\Events\LogEntry;

use App\Models\LogEntry;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class EventWasLogged
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var LogEntry
     */
    private $entry;

    /**
     * Create a new event instance.
     *
     * @param LogEntry $entry
     */
    public function __construct(LogEntry $entry)
    {
        $this->entry = $entry;
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
     * @return LogEntry
     */
    public function getEntry()
    {
        return $this->entry;
    }
}
