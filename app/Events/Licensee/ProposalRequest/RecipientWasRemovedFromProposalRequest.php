<?php

namespace App\Events\Licensee\ProposalRequest;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RecipientWasRemovedFromProposalRequest
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var int
     */
    private $requestId;

    /**
     * @var int
     */
    private $hotelId;

    /**
     * @var int
     */
    private $userId;

    /**
     * Create a new event instance.
     *
     * @param int $requestId
     * @param int $hotelId
     * @param int $userId
     */
    public function __construct($requestId, $hotelId, $userId)
    {
        $this->requestId = $requestId;
        $this->hotelId = $hotelId;
        $this->userId = $userId;
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
     * @return int
     */
    public function getHotelId()
    {
        return $this->hotelId;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }
}

