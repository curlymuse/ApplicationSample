<?php

namespace App\Events\Hotel\Proposal;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ProposalWasCreated
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var int
     */
    private $proposalId;

    /**
     * @var int
     */
    private $proposalRequestId;

    /**
     * @var int
     */
    private $hotelId;

    /**
     * Create a new event instance.
     *
     * @param int $proposalId
     * @param int $proposalRequestId
     * @param int $hotelId
     */
    public function __construct($proposalId, $proposalRequestId, $hotelId)
    {
        $this->proposalId = $proposalId;
        $this->proposalRequestId = $proposalRequestId;
        $this->hotelId = $hotelId;
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
    public function getProposalId()
    {
        return $this->proposalId;
    }

    /**
     * @return int
     */
    public function getProposalRequestId()
    {
        return $this->proposalRequestId;
    }

    /**
     * @return int
     */
    public function getHotelId()
    {
        return $this->hotelId;
    }
}
