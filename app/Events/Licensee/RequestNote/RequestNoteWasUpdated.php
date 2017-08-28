<?php

namespace App\Events\Licensee\RequestNote;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RequestNoteWasUpdated extends Event
{
    use SerializesModels;

    /**
     * @var int
     */
    private $noteId;

    /**
     * @var string
     */
    private $body;

    /**
     * Create a new event instance.
     *
     * @param int $noteId
     * @param string $body
     */
    public function __construct($noteId, $body)
    {
        $this->noteId = $noteId;
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
    public function getNoteId()
    {
        return $this->noteId;
    }
}
