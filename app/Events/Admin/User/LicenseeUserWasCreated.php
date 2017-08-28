<?php

namespace App\Events\Admin\User;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class LicenseeUserWasCreated extends Event
{
    use SerializesModels;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $name;

    /**
     * @var \App\Models\Licensee
     */
    private $licensee;

    /**
     * @var string
     */
    private $roleSlug;

    /**
     * @var array
     */
    private $attributes;

    /**
     * @var string
     */
    private $tempPassword;

    /**
     * Create a new event instance.
     *
     * @param string $email
     * @param string $name
     * @param \App\Models\Licensee $licensee
     * @param string $roleSlug
     * @param string $tempPassword
     * @param array $attributes
     */
    public function __construct($email, $name, $licensee, $roleSlug, $tempPassword, $attributes = [])
    {
        $this->email = $email;
        $this->name = $name;
        $this->licensee = $licensee;
        $this->roleSlug = $roleSlug;
        $this->attributes = $attributes;
        $this->tempPassword = $tempPassword;
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
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return \App\Models\Licensee
     */
    public function getLicensee()
    {
        return $this->licensee;
    }

    /**
     * @return string
     */
    public function getRoleSlug()
    {
        return $this->roleSlug;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @return string
     */
    public function getTempPassword()
    {
        return $this->tempPassword;
    }
}
