<?php

namespace App\Events\Hotel\Proposal;

use App\Events\Contracts\LoggableEvent;
use App\Models\Hotel;
use App\Models\Proposal;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ProposalWasVisitedByHotel implements LoggableEvent
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
     * @var int
     */
    private $hotelId;

    /**
     * Create a new event instance.
     *
     * @param int $userId
     * @param int $proposalId
     * @param int $hotelId
     */
    public function __construct($userId, $proposalId, $hotelId)
    {
        $this->userId = $userId;
        $this->proposalId = $proposalId;
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
        return Hotel::class;
    }

    /**
     * @return int
     */
    public function getAccountId()
    {
        return $this->hotelId;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return 'proposal:visit-hotel';
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
        return $this->proposalId;
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
            '%s (%s) visited the "%s" proposal',
            userIdToName($this->userId),
            hotelIdToName($this->hotelId),
            proposalIdToEventName($this->proposalId)
        );
    }
}
