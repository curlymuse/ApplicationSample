<?php

namespace App\Events\Licensee\Proposal;

use App\Events\Contracts\LoggableEvent;
use App\Models\Licensee;
use App\Models\Proposal;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ProposalWasVisitedByLicensee implements LoggableEvent
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var int
     */
    private $userId;

    /**
     * @var int
     */
    private $licenseeId;

    /**
     * @var int
     */
    private $hotelId;

    /**
     * @var Proposal
     */
    private $proposal;

    /**
     * Create a new event instance.
     *
     * @param int $userId
     * @param int $licenseeId
     * @param Proposal $proposal
     */
    public function __construct(Proposal $proposal, $userId, $licenseeId)
    {
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
        return 'proposal:visit-licensee';
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
            '%s (%s) visited the %s "%s" proposal',
            userIdToName($this->userId),
            licenseeIdToName($this->licenseeId),
            hotelIdToName($this->proposal->hotel_id),
            proposalIdToEventName($this->proposal->id)
        );
    }
}
