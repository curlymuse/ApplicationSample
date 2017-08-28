<?php

namespace App\Events\Licensee\ProposalRequest;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class HotelWasAddedToProposalRequest
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var int
     */
    private $hotelId;

    /**
     * @var int
     */
    private $requestId;

    /**
     * Create a new event instance.
     *
     * @param int $hotelId
     * @param int $requestId
     */
    public function __construct($hotelId, $requestId)
    {
        $this->hotelId = $hotelId;
        $this->requestId = $requestId;
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
    public function getHotelId()
    {
        return $this->hotelId;
    }

    /**
     * @return int
     */
    public function getRequestId()
    {
        return $this->requestId;
    }
}
