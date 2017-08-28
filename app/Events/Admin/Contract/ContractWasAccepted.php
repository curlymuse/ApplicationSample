<?php

namespace App\Events\Admin\Contract;

use App\Events\Contracts\LoggableEvent;
use App\Models\Contract;
use App\Models\Hotel;
use App\Models\Licensee;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ContractWasAccepted implements LoggableEvent
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var int
     */
    private $userId;

    /**
     * @var string
     */
    private $userType;

    /**
     * @var string
     */
    private $signature;

    /**
     * @var Contract
     */
    private $contract;

    /**
     * Create a new event instance.
     *
     * @param Contract $contract
     * @param int $userId
     * @param string $userType
     * @param string $signature
     */
    public function __construct(Contract $contract, $userId, $userType, $signature)
    {
        $this->userId = $userId;
        $this->userType = $userType;
        $this->signature = $signature;
        $this->contract = $contract;
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
    public function getContractId()
    {
        return $this->contract->id;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getUserType()
    {
        return $this->userType;
    }

    /**
     * @return string
     */
    public function getSignature()
    {
        return $this->signature;
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
        return $this->contract->proposal->hotel_id;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return sprintf(
            'contract:accept-for-%s',
            $this->userType
        );
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
        return ($this->userType == 'owner')
            ? ($this->contract->is_client_owned)
                ? $this->getDescriptionForOwnerUser()
                : $this->getDescriptionForClientUser()
            : $this->getDescriptionForHotelUser();
    }

    private function getDescriptionForHotelUser()
    {
        return sprintf(
            '%s (%s) signed the "%s" contract',
            userIdToName($this->userId),
            hotelIdToName($this->contract->proposal->hotel_id),
            proposalIdToEventName($this->contract->proposal_id)
        );
    }

    private function getDescriptionForClientUser()
    {
        return sprintf(
            '%s (%s) signed the %s "%s" contract',
            userIdToName($this->userId),
            clientIdToName($this->contract->proposal->proposalRequest->client_id),
            hotelIdToName($this->contract->proposal->hotel_id),
            proposalIdToEventName($this->contract->proposal_id)
        );
    }

    private function getDescriptionForOwnerUser()
    {
        return sprintf(
            '%s (%s) signed the %s "%s" contract',
            userIdToName($this->userId),
            licenseeIdToName($this->contract->proposal->proposalRequest->event->licensee_id),
            hotelIdToName($this->contract->proposal->hotel_id),
            proposalIdToEventName($this->contract->proposal_id)
        );
    }
}
