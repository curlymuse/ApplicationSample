<?php

namespace App\Events\Licensee\ProposalRequest;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RequestQuestionGroupWasCreated
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var int
     */
    private $requestId;

    /**
     * @var string
     */
    private $name;

    /**
     * Create a new event instance.
     *
     * @param int $requestId
     * @param string $name
     */
    public function __construct($requestId, $name)
    {
        $this->requestId = $requestId;
        $this->name = $name;
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
    public function getRequestId()
    {
        return $this->requestId;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
