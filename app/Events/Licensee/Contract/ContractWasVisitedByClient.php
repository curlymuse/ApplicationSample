<?php

namespace App\Events\Licensee\Contract;

use App\Events\Contracts\LoggableEvent;
use App\Models\Client;
use App\Models\Contract;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ContractWasVisitedByClient implements LoggableEvent
{
    use InteractsWithSockets, SerializesModels;

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
        return Client::class;
    }

    /**
     * @return int
     */
    public function getAccountId()
    {
        return $this->contract->proposal->proposalRequest->client->id;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return 'contract:view-client';
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
            clientIdToName($this->contract->proposal->proposalRequest->client_id),
            hotelIdToName($this->contract->proposal->hotel_id),
            proposalIdToEventName($this->contract->proposal_id)
        );
    }
}
