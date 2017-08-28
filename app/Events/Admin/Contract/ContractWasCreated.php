<?php

namespace App\Events\Admin\Contract;

use App\Events\Contracts\LoggableEvent;
use App\Events\Event;
use App\Models\Contract;
use App\Models\Licensee;
use App\Models\Proposal;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ContractWasCreated extends Event implements LoggableEvent
{
    use SerializesModels;

    /**
     * @var Contract
     */
    private $contract;

    /**
     * @var int
     */
    private $userId;

    /**
     * Create a new event instance.
     *
     * @param Contract $contract
     * @param int $userId
     */
    public function __construct(Contract $contract, $userId)
    {
        $this->contract = $contract;
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
     * @return Contract
     */
    public function getContract()
    {
        return $this->contract;
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
        return $this->contract->proposal->proposalRequest->event->licensee_id;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return 'proposal:accept';
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
        return $this->contract->proposal_id;
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
            '%s (%s) accepted the %s "%s" proposal',
            userIdToName($this->userId),
            licenseeIdToName($this->getAccountId()),
            hotelIdToName($this->contract->proposal->hotel_id),
            proposalIdToEventName($this->contract->proposal_id)
        );
    }
}
