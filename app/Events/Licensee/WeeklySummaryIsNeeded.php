<?php

namespace App\Events\Licensee;

use App\Models\Licensee;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class WeeklySummaryIsNeeded
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var Licensee
     */
    private $licensee;

    /**
     * Create a new event instance.
     *
     * @param Licensee $licensee
     */
    public function __construct(Licensee $licensee)
    {
        $this->licensee = $licensee;
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
     * @return Licensee
     */
    public function getLicensee()
    {
        return $this->licensee;
    }
}
