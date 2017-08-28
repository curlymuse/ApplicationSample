<?php

namespace App\Events\Licensee;

use App\Models\Hotel;
use App\Models\ProposalRequest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ProposalRequestCutoffIsApproaching
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var ProposalRequest
     */
    private $request;

    /**
     * @var Hotel
     */
    private $hotel;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ProposalRequest $request, Hotel $hotel)
    {
        $this->request = $request;
        $this->hotel = $hotel;
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
     * @return ProposalRequest
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return Hotel
     */
    public function getHotel()
    {
        return $this->hotel;
    }
}
