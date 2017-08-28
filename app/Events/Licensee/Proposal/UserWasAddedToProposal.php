<?php

namespace App\Events\Licensee\Proposal;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserWasAddedToProposal extends Event
{
    use SerializesModels;

    /**
     * @var int
     */
    private $proposalId;

    /**
     * @var int
     */
    private $userId;

    /**
     * Create a new event instance.
     *
     * @param int $proposalId
     * @param int $userId
     */
    public function __construct($proposalId, $userId)
    {
        $this->proposalId = $proposalId;
        $this->userId = $userId;
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
