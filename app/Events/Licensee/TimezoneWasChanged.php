<?php

namespace App\Events\Licensee;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TimezoneWasChanged
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var int
     */
    private $licenseeId;

    /**
     * @var string
     */
    private $oldTimezone;

    /**
     * @var string
     */
    private $newTimezone;

    /**
     * Create a new event instance.
     *
     * @param int $licenseeId
     * @param string $oldTimezone
     * @param string $newTimezone
     */
    public function __construct($licenseeId, $oldTimezone, $newTimezone)
    {
        $this->licenseeId = $licenseeId;
        $this->oldTimezone = $oldTimezone;
        $this->newTimezone = $newTimezone;
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
     * @return string
     */
    public function getOldTimezone()
    {
        return $this->oldTimezone;
    }

    /**
     * @return string
     */
    public function getNewTimezone()
    {
        return $this->newTimezone;
    }
}
