<?php

namespace App\Events\Licensee\ProposalRequest;

use App\Events\Contracts\LoggableEvent;
use App\Events\Event;
use App\Models\Licensee;
use App\Models\ProposalRequest;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ProposalRequestsWereDisbursed extends Event implements LoggableEvent
{
    use SerializesModels;

    /**
     * @var ProposalRequest
     */
    private $request;

    /**
     * @var int
     */
    private $userId;

    /**
     * Create a new event instance.
     *
     * @param ProposalRequest $request
     * @param int $userId
     */
    public function __construct(ProposalRequest $request, $userId)
    {
        $this->request = $request;
        $this->userId = $userId;
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
     * @return ProposalRequest
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Return the User ID
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
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
        return $this->request->event->licensee_id;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return 'proposal-request:disburse';
    }

    /**
     * @return string
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
        return $this->request->id;
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
