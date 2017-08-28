<?php

namespace App\Events\User;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserSubmittedProposal extends Event
{
    use SerializesModels;

    /**
     * @var int
     */
    private $userId;

    /**
     * @var int
     */
    private $proposalId;

    /**
     * Create a new event instance.
     *
     * @param int $userId
     * @param int $proposalId
     */
    public function __construct($userId, $proposalId)
    {
        $this->userId = $userId;
        $this->proposalId = $proposalId;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return int
     */
    public function getProposalId()
    {
        return $this->proposalId;
    }
}
