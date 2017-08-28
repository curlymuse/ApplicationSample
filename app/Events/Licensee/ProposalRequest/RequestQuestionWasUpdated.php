<?php

namespace App\Events\Licensee\ProposalRequest;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RequestQuestionWasUpdated
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var int
     */
    private $questionId;

    /**
     * @var string
     */
    private $questionText;

    /**
     * Create a new event instance.
     *
     * @param int $questionId
     * @param string $questionText
     */
    public function __construct($questionId, $questionText)
    {
        $this->questionId = $questionId;
        $this->questionText = $questionText;
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
    public function getQuestionId()
    {
        return $this->questionId;
    }

    /**
     * @return string
     */
    public function getQuestionText()
    {
        return $this->questionText;
    }
}
