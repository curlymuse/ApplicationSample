<?php

namespace App\Events\Hotel\Proposal;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ProposalActionOccurred
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var int
     */
    private $userId;

    /**
     * @var int
     */
    private $proposalId;

    /**
     * @var string
     */
    private $action;

    /**
     * @var null
     */
    private $notes;

    /**
     * Create a new event instance.
     *
     * @param int $userId
     * @param int $proposalId
     * @param string $action
     * @param null $notes
     */
    public function __construct($userId, $proposalId, $action, $notes = null)
    {
        $this->userId = $userId;
        $this->proposalId = $proposalId;
        $this->action = $action;
        $this->notes = $notes;
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

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return null
     */
    public function getNotes()
    {
        return $this->notes;
    }
}
