<?php

namespace App\Events;

use App\Events\Contracts\LoggableEvent;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class EmailWasSent implements LoggableEvent
{
    use InteractsWithSockets, SerializesModels;

    /**
     * @var User
     */
    private $user;

    /**
     * @var string
     */
    private $mailableClass;

    /**
     * @var array
     */
    private $arguments;

    /**
     * @var int|null
     */
    private $subjectType;

    /**
     * @var int|null
     */
    private $subjectId;

    /**
     * Create a new event instance.
     *
     * @param User $user
     * @param string $mailableClass
     * @param null|int $subjectType
     * @param null|int $subjectId
     * @internal param array $arguments
     */
    public function __construct(User $user, $mailableClass, $subjectType = null, $subjectId = null)
    {
        $this->user = $user;
        $this->mailableClass = $mailableClass;
        $this->subjectType = $subjectType;
        $this->subjectId = $subjectId;
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
        return User::class;
    }

    /**
     * @return int
     */
    public function getAccountId()
    {
        return $this->getUserId();
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return 'mail:sent';
    }

    /**
     * @return string
     */
    public function getSubjectType()
    {
        return $this->subjectType;
    }

    /**
     * @return int
     */
    public function getSubjectId()
    {
        return $this->subjectId;
    }

    /**
     * @return string
     */
    public function getNotes()
    {
        return $this->mailableClass;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        // TODO: Implement getDescription() method.
    }
}
