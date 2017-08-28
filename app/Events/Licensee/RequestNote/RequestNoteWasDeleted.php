<?php

namespace App\Events\Licensee\RequestNote;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RequestNoteWasDeleted extends Event
{
    use SerializesModels;

    /**
     * @var int
     */
    private $noteId;

    /**
     * Create a new event instance.
     *
     * @param int $noteId
     */
    public function __construct($noteId)
    {
        $this->noteId = $noteId;
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
     * @return int
     */
    public function getNoteId()
    {
        return $this->noteId;
    }
}
