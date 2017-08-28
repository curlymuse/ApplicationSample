<?php

namespace App\Events\Hotel\Proposal;

use App\Events\Contracts\EventWithUser;
use App\Events\Contracts\LoggableEvent;
use App\Models\Hotel;
use App\Models\Licensee;
use App\Models\Proposal;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ProposalWasSubmitted implements EventWithUser, LoggableEvent
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var int
     */
    private $userId;

    /**
     * @var int
     */
    private $eventDateRangeId;

    /**
     * @var Proposal
     */
    private $proposal;

    /**
     * Create a new event instance.
     *
     * @param Proposal $proposal
     * @param int $eventDateRangeId
     * @param int $userId
     * @internal param int $proposalId
     */
    public function __construct(Proposal $proposal, $eventDateRangeId, $userId)
    {
        $this->userId = $userId;
        $this->eventDateRangeId = $eventDateRangeId;
        $this->proposal = $proposal;
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
        return $this->proposal->id;
    }

    /**
     * @return int
     */
    public function getEventDateRangeId()
    {
        return $this->eventDateRangeId;
    }

    /**
     * @return string
     */
    public function getAccountType()
    {
        return Licensee::class;
    }

    /**
     * @return int
     */
    public function getAccountId()
    {
        return $this->proposal->proposalRequest->event->licensee_id;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return 'bid:receive';
    }

    /**
     * @return string
     */
    public function getSubjectType()
    {
        return Proposal::class;
    }

    /**
     * @return int
     */
    public function getSubjectId()
    {
        return $this->proposal->id;
    }

    /**
     * @return string
     */
    public function getNotes()
    {
        return null;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return sprintf(
            '%s (%s) submitted the "%s" proposal',
            userIdToName($this->userId),
            $this->proposal->hotel->name,
            $this->proposal->proposalRequest->event->name
        );
    }
}
