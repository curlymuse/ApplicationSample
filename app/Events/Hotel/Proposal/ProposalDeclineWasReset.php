<?php

namespace App\Events\Hotel\Proposal;

use App\Events\Contracts\LoggableEvent;
use App\Models\Licensee;
use App\Models\Proposal;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ProposalDeclineWasReset implements LoggableEvent
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var int
     */
    private $eventDateRangeId;

    /**
     * @var int
     */
    private $userId;

    /**
     * @var int
     */
    private $licenseeId;

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
     * @param int $licenseeId
     */
    public function __construct(Proposal $proposal, $eventDateRangeId, $userId, $licenseeId)
    {
        $this->eventDateRangeId = $eventDateRangeId;
        $this->userId = $userId;
        $this->licenseeId = $licenseeId;
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
     * Return the User ID
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
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
        return $this->licenseeId;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return 'proposal:reset-decline';
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
        // TODO: Implement getNotes() method.
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return sprintf(
            '%s (%s) reset the %s "%s" proposal',
            userIdToName($this->userId),
            licenseeIdToName($this->licenseeId),
            hotelIdToName($this->proposal->hotel_id),
            proposalIdToEventName($this->proposal->id)
        );
    }
}
