<?php

namespace App\Events\Licensee\RequestNote;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RequestNoteWasCreated extends Event
{
    use SerializesModels;

    /**
     * @var int
     */
    private $requestId;

    /**
     * @var int
     */
    private $authorId;

    /**
     * @var string
     */
    private $body;

    /**
     * Create a new event instance.
     *
     * @param int $requestId
     * @param int $authorId
     * @param string $body
     */
    public function __construct($requestId, $authorId, $body)
    {
        $this->requestId = $requestId;
        $this->authorId = $authorId;
        $this->body = $body;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return int
     */
    public function getAuthorId()
    {
        return $this->authorId;
    }

    /**
     * @return int
     */
    public function getRequestId()
    {
        return $this->requestId;
    }
}
