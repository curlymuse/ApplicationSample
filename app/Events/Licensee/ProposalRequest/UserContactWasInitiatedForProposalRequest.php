<?php

namespace App\Events\Licensee\ProposalRequest;

use App\Events\Contracts\LoggableEvent;
use App\Models\Hotel;
use App\Models\Licensee;
use App\Models\ProposalRequest;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserContactWasInitiatedForProposalRequest implements LoggableEvent
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var User
     */
    private $user;

    /**
     * @var ProposalRequest
     */
    private $request;

    /**
     * @var Hotel
     */
    private $hotel;

    /**
     * @var array
     */
    private $otherRecipients;

    /**
     * @var int
     */
    private $disbursingUserId;

    /**
     * Create a new event instance.
     *
     * @param User $user
     * @param ProposalRequest $request
     * @param Hotel $hotel
     * @param array $otherRecipients
     * @param int $disbursingUserId
     * @internal param array $otherReceipients
     */
    public function __construct(
        User $user,
        ProposalRequest $request,
        Hotel $hotel,
        $otherRecipients = [],
        $disbursingUserId
    )
    {
        $this->user = $user;
        $this->request = $request;
        $this->hotel = $hotel;
        $this->otherRecipients = $otherRecipients;
        $this->disbursingUserId = $disbursingUserId;
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
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return ProposalRequest
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return Hotel
     */
    public function getHotel()
    {
        return $this->hotel;
    }

    /**
     * @return array
     */
    public function getOtherRecipients()
    {
        return $this->otherRecipients;
    }

    /**
     * Return the User ID
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->user->id;
    }

    /**
     * @return string
     */
    public function getAccountType()
    {
        return Hotel::class;
    }

    /**
     * @return int
     */
    public function getAccountId()
    {
        return $this->hotel->id;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return 'proposal-request:received-by-hotel';
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
        return sprintf(
            '%s (%s) sent the "%s" RFP to %s (%s)',
            userIdToName($this->disbursingUserId),
            $this->request->event->licensee->company_name,
            $this->request->event->name,
            $this->user->name,
            $this->hotel->name
        );
    }
}
