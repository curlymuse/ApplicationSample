<?php

namespace App\Events\ChangeOrder;

use App\Events\Contracts\LoggableEvent;
use App\Models\ChangeOrder;
use App\Models\Contract;
use App\Models\Hotel;
use App\Models\Licensee;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ChangeOrderWasCreated implements LoggableEvent
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var ChangeOrder
     */
    private $changeOrder;

    /**
     * @var User
     */
    private $user;

    /**
     * @var string
     */
    private $userType;

    /**
     * Create a new event instance.
     *
     * @param ChangeOrder $changeOrder
     * @param User $user
     * @param string $userType
     */
    public function __construct(ChangeOrder $changeOrder, User $user, $userType)
    {
        $this->changeOrder = $changeOrder;
        $this->user = $user;
        $this->userType = $userType;
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
     * @return ChangeOrder
     */
    public function getChangeOrder()
    {
        return $this->changeOrder;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
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
        return $this->changeOrder->contract->proposal->hotel_id;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return 'change-order:create';
    }

    /**
     * @return string
     */
    public function getSubjectType()
    {
        return Contract::class;
    }

    /**
     * @return int
     */
    public function getSubjectId()
    {
        return $this->changeOrder->contract_id;
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
        return ($this->userType == 'licensee')
            ? $this->getDescriptionForLicenseeUser()
            : $this->getDescriptionForHotelUser();
    }

    private function getDescriptionForHotelUser()
    {
        return sprintf(
            '%s (%s) sent modifications to the "%s" contract',
            $this->user->name,
            hotelIdToName($this->changeOrder->contract->proposal->hotel_id),
            proposalIdToEventName($this->changeOrder->contract->proposal_id)
        );
    }

    private function getDescriptionForLicenseeUser()
    {
        return sprintf(
            '%s (%s) sent modifications to the %s "%s" contract',
            $this->user->name,
            licenseeIdToName($this->changeOrder->contract->proposal->proposalRequest->event->licensee_id),
            hotelIdToName($this->changeOrder->contract->proposal->hotel_id),
            proposalIdToEventName($this->changeOrder->contract->proposal_id)
        );
    }
}
