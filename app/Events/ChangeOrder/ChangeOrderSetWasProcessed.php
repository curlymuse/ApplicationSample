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

class ChangeOrderSetWasProcessed implements LoggableEvent
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var \App\Models\User
     */
    protected $user;

    /**
     * @var \App\Models\ChangeOrder
     */
    protected $changeOrder;

    /**
     * @var array
     */
    private $changes;

    /**
     * Create a new event instance.
     *
     * @param ChangeOrder $changeOrder
     * @param User $user
     * @param array $changes
     */
    public function __construct($changeOrder, $user, $changes = [])
    {
        $this->changeOrder= $changeOrder;
        $this->user= $user;
        $this->changes = $changes;
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
     * @return int
     */
    public function getChangeOrder()
    {
        return $this->changeOrder;
    }

    /**
     * @return int
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return array
     */
    public function getChanges()
    {
        return $this->changes;
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
        return  $this->changeOrder->contract->proposal->hotel_id;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return 'change-order:respond';
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
        // TODO: Implement getNotes() method.
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return ($this->changeOrder->initiated_by_party == 'hotel')
            ? ($this->changeOrder->contract->is_client_owned)
                ? $this->getDescriptionForLicenseeUser()
                : $this->getDescriptionForClientUser()
            : $this->getDescriptionForHotelUser();
    }

    private function getDescriptionForHotelUser()
    {
        return sprintf(
            '%s (%s) replied to the "%s" contract modifications',
            $this->user->name,
            hotelIdToName($this->changeOrder->contract->proposal->hotel_id),
            proposalIdToEventName($this->changeOrder->contract->proposal_id)
        );
    }

    private function getDescriptionForClientUser()
    {
        return sprintf(
            '%s (%s) replied to the %s "%s" contract modifications',
            $this->user->name,
            clientIdToName($this->changeOrder->contract->proposal->proposalRequest->client_id),
            hotelIdToName($this->changeOrder->contract->proposal->hotel_id),
            proposalIdToEventName($this->changeOrder->contract->proposal_id)
        );
    }

    private function getDescriptionForLicenseeUser()
    {
        return sprintf(
            '%s (%s) replied to the %s "%s" contract modifications',
            $this->user->name,
            licenseeIdToName($this->changeOrder->contract->proposal->proposalRequest->event->licensee_id),
            hotelIdToName($this->changeOrder->contract->proposal->hotel_id),
            proposalIdToEventName($this->changeOrder->contract->proposal_id)
        );
    }
}
