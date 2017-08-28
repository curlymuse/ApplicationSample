<?php

namespace App\Events\Licensee\ProposalRequest;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class AccommodationsWereUpdated extends Event
{
    use SerializesModels;

    /**
     * @var int
     */
    private $requestId;

    /**
     * @var array
     */
    private $accommodations;

    /**
     * Create a new event instance.
     *
     * @param int $requestId
     * @param array $accommodations
     */
    public function __construct($requestId, $accommodations = [])
    {
        $this->requestId = $requestId;
        $this->accommodations = $accommodations;
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
    public function getRequestId()
    {
        return $this->requestId;
    }

    /**
     * @return array
     */
    public function getAccommodations()
    {
        return $this->accommodations;
    }
}
