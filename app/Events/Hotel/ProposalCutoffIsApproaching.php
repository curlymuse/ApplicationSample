<?php

namespace App\Events\Hotel;

use App\Models\Proposal;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ProposalCutoffIsApproaching
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var Proposal
     */
    private $proposal;

    /**
     * Create a new event instance.
     *
     * @param Proposal $proposal
     *
     * @return void
     */
    public function __construct(Proposal $proposal)
    {
        //
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
     * @return Proposal
     */
    public function getProposal()
    {
        return $this->proposal;
    }
}
