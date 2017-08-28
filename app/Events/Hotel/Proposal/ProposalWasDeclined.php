<?php

namespace App\Events\Hotel\Proposal;

use App\Events\Contracts\LoggableEvent;
use App\Events\Event;
use App\Models\Licensee;
use App\Models\Proposal;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ProposalWasDeclined extends Event implements LoggableEvent
{
    use SerializesModels;

    /**
     * @var int|null
     */
    private $userId;

    /**
     * @var null|string
     */
    private $reason;

    /**
     * @var string
     */
    private $userType;

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
     * @param string $userType
     * @param null|string $reason
     */
    public function __construct(Proposal $proposal, $eventDateRangeId, $userId, $userType, $reason = null)
    {
        $this->userId = $userId;
        $this->reason = $reason;
        $this->userType = $userType;
        $this->eventDateRangeId = $eventDateRangeId;
        $this->proposal = $proposal;
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
    public function getProposalId()
    {
        return $this->proposal->id;
    }

    /**
     * @return int|null
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return null|string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * @return string
     */
    public function getUserType()
    {
        return $this->userType;
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
        return 'proposal:decline';
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
        return sprintf('Declined by %s', $this->userType);
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return sprintf(
            '%s (%s) declined the %s "%s" proposal',
            userIdToName($this->userId),
            licenseeIdToName($this->getAccountId()),
            hotelIdToName($this->proposal->hotel_id),
            proposalIdToEventName($this->getProposalId())
        );
    }
}
