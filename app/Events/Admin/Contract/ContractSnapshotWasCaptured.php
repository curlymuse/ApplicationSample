<?php

namespace App\Events\Admin\Contract;

use App\Models\Contract;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ContractSnapshotWasCaptured
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var Contract
     */
    private $contract;

    /**
     * Create a new event instance.
     *
     * @param Contract $contract
     */
    public function __construct(Contract $contract)
    {
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
     * @return Contract
     */
    public function getContract()
    {
        return $this->contract;
    }
}
