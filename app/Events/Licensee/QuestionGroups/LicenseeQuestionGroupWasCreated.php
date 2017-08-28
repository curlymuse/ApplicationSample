<?php

namespace App\Events\Licensee\QuestionGroups;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class LicenseeQuestionGroupWasCreated
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var int
     */
    private $licenseeId;

    /**
     * @var string
     */
    private $name;

    /**
     * Create a new event instance.
     *
     * @param int $licenseeId
     * @param string $name
     */
    public function __construct($licenseeId, $name)
    {
        $this->licenseeId = $licenseeId;
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
     * @param int $licenseeId
     * @return LicenseeQuestionGroupWasCreated
     */
    public function setLicenseeId($licenseeId)
    {
        $this->licenseeId = $licenseeId;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
