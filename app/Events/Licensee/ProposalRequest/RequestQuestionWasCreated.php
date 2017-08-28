<?php

namespace App\Events\Licensee\ProposalRequest;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RequestQuestionWasCreated
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var int
     */
    private $groupId;

    /**
     * @var string
     */
    private $question;

    /**
     * Create a new event instance.
     *
     * @param int $groupId
     * @param string $question
     */
    public function __construct($groupId, $question)
    {
        $this->groupId = $groupId;
        $this->question = $question;
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
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * @return string
     */
    public function getQuestion()
    {
        return $this->question;
    }
}
