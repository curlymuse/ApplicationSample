<?php

namespace App\Events\Admin\Contract;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ContractWasDeclined
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var int
     */
    private $contractId;

    /**
     * @var int
     */
    private $userId;

    /**
     * @var string
     */
    private $userType;

    /**
     * @var null|string
     */
    private $reason;

    /**
     * Create a new event instance.
     *
     * @param int $contractId
     * @param int $userId
     * @param string $userType
     * @param null|string $reason
     */
    public function __construct($contractId, $userId, $userType, $reason = null)
    {
        $this->contractId = $contractId;
        $this->userId = $userId;
        $this->userType = $userType;
        $this->reason = $reason;
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
        return $this->contractId;
    }

    /**
     * @return int
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
}
