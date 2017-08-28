<?php

namespace App\Events\Licensee\ProposalRequest;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserWasAddedToProposalRequest
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var int
     */
    private $proposalRequestId;

    /**
     * @var int
     */
    private $userId;

    /**
     * @var array
     */
    private $contactInfo;

    /**
     * Create a new event instance.
     *
     * @param int $proposalRequestId
     * @param int $userId
     * @param array $contactInfo
     */
    public function __construct($proposalRequestId, $userId, $contactInfo = [])
    {
        $this->proposalRequestId = $proposalRequestId;
        $this->userId = $userId;
        $this->contactInfo = $contactInfo;
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
    public function getProposalRequestId()
    {
        return $this->proposalRequestId;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return array
     */
    public function getContactInfo()
    {
        return $this->contactInfo;
    }
}
