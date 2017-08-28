<?php

namespace App\Events\Admin\Hotel;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class HotelWasDeleted extends Event
{
    use SerializesModels;

    /**
     * @var int
     */
    private $hotelId;

    /**
     * Create a new event instance.
     *
     * @param int $hotelId
     */
    public function __construct($hotelId)
    {
        $this->hotelId = $hotelId;
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
    public function getHotelId()
    {
        return $this->hotelId;
    }
}
