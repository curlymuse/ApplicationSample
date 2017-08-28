<?php

namespace App\Events\Licensee\Profile;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class LicenseeLogoWasUpdated
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var int
     */
    private $licenseeId;

    /**
     * @var string
     */
    private $logoUrl;

    /**
     * Create a new event instance.
     *
     * @param int $licenseeId
     * @param string $logoUrl
     */
    public function __construct($licenseeId, $logoUrl)
    {
        $this->licenseeId = $licenseeId;
        $this->logoUrl = $logoUrl;
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
    public function getLogoUrl()
    {
        return $this->logoUrl;
    }
}
