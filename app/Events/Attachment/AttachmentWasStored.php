<?php

namespace App\Events\Attachment;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class AttachmentWasStored
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var string
     */
    private $attachableType;

    /**
     * @var int
     */
    private $attachableId;

    /**
     * @var string
     */
    private $url;

    /**
     * @var int
     */
    private $userId;

    /**
     * @var null|string
     */
    private $category;

    /**
     * Create a new event instance.
     *
     * @param string $attachableType
     * @param int $attachableId
     * @param string $url
     * @param int $userId
     * @param string|null $category
     *
     * @return void
     */
    public function __construct(
        $attachableType,
        $attachableId,
        $url,
        $userId,
        $category = null
    )
    {
        $this->attachableType = $attachableType;
        $this->attachableId = $attachableId;
        $this->url = $url;
        $this->userId = $userId;
        $this->category = $category;
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
     * @return string
     */
    public function getAttachableType()
    {
        return $this->attachableType;
    }

    /**
     * @return int
     */
    public function getAttachableId()
    {
        return $this->attachableId;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return null|string
     */
    public function getCategory()
    {
        return $this->category;
    }
}
