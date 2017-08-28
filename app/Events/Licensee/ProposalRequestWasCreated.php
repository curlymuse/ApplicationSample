<?php

namespace App\Events\Licensee;

use App\Events\Contracts\LoggableEvent;
use App\Events\Event;
use App\Models\Licensee;
use App\Models\ProposalRequest;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ProposalRequestWasCreated extends Event implements LoggableEvent
{
    use SerializesModels;

    /**
     * @var array
     */
    private $attributes;

    /**
     * @var
     */
    private $eventId;

    /**
     * @var int
     */
    private $userId;

    /**
     * @var int
     */
    private $licenseeId;

    /**
     * @var int
     */
    private $requestId;

    /**
     * Create a new event instance.
     *
     * @param int $eventId
     * @param int $requestId
     * @param int $userId
     * @param int $licenseeId
     * @param array $attributes
     */
    public function __construct($eventId, $requestId, $userId, $licenseeId, $attributes)
    {
        $this->attributes = $attributes;
        $this->eventId = $eventId;
        $this->userId = $userId;
        $this->licenseeId = $licenseeId;
        $this->requestId = $requestId;
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
     * @return mixed
     */
    public function getEventId()
    {
        return $this->eventId;
    }

    /**
     * @return int
     */
    public function getLicenseeId()
    {
        return $this->licenseeId;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return 'proposal-request:create';
    }

    /**
     * @return string
     */
    public function getAccountType()
    {
        return Licensee::class;
    }

    /**
     * @return int
     */
    public function getAccountId()
    {
        return $this->licenseeId;
    }

    /**
     * @return mixed
     */
    public function getSubjectType()
    {
        return ProposalRequest::class;
    }

    /**
     * @return int
     */
    public function getSubjectId()
    {
        return $this->requestId;
    }

    /**
     * @return string
     */
    public function getNotes()
    {
        return null;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        // TODO: Implement getDescription() method.
    }
}
