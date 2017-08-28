<?php

namespace App\Events\Admin\Hotel;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class HotelWasUpdated extends Event
{
    use SerializesModels;

    /**
     * @var int
     */
    private $hotelId;

    /**
     * @var array
     */
    private $attributes;

    /**
     * Create a new event instance.
     *
     * @param int $hotelId
     * @param array $attributes
     */
    public function __construct($hotelId, $attributes = [])
    {
        $this->hotelId = $hotelId;
        $this->attributes = $attributes;
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
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @return int
     */
    public function getHotelId()
    {
        return $this->hotelId;
    }
}
