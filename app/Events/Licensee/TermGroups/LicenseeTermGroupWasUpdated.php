<?php

namespace App\Events\Licensee\TermGroups;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class LicenseeTermGroupWasUpdated
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var int
     */
    private $groupId;

    /**
     * @var string
     */
    private $newName;

    /**
     * Create a new event instance.
     *
     * @param int $groupId
     * @param string $newName
     */
    public function __construct($groupId, $newName)
    {
        $this->groupId = $groupId;
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
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * @return string
     */
    public function getNewName()
    {
        return $this->newName;
    }
}
