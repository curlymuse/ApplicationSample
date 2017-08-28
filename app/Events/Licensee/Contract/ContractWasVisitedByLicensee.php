<?php

namespace App\Events\Licensee\Contract;

use App\Events\Contracts\LoggableEvent;
use App\Models\Contract;
use App\Models\Licensee;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ContractWasVisitedByLicensee implements LoggableEvent
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var Contract
     */
    private $contract;

    /**
     * @var
     */
    private $userId;

    /**
     * @var
     */
    private $licenseeId;

    /**
     * Create a new event instance.
     *
     * @param Contract $contract
     * @param $userId
     * @param $licenseeId
     */
    public function __construct(Contract $contract, $userId, $licenseeId)
    {
        $this->contract = $contract;
        $this->userId = $userId;
        $this->licenseeId = $licenseeId;
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
        return 'contract:visit-licensee';
    }

    /**
     * @return string
     */
    public function getSubjectType()
    {
        return Contract::class;
    }

    /**
     * @return int
     */
    public function getSubjectId()
    {
        return $this->contract->id;
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
            '%s (%s) visited the %s "%s" contract',
            userIdToName($this->userId),
            licenseeIdToName($this->licenseeId),
            hotelIdToName($this->contract->proposal->hotel_id),
            proposalIdToEventName($this->contract->proposal_id)
        );
    }
}
