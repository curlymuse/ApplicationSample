<?php

namespace App\Traits;

use App\Events\EmailWasSent;
use App\Support\MyMailable;
use Illuminate\Contracts\Mail\Mailable;

trait SendsMailables
{
    /**
     * Send a message to a collection of Users, setting the "to" property
     * of each message to the user
     *
     * @param $recipients
     * @param string $mailableClass
     * @param array $arguments
     */
    protected function sendMailable($recipients, $mailableClass, $arguments = [], $subject = null)
    {
        foreach ($recipients as $user) {
            $mailable = new $mailableClass(...$arguments);
            $mailable->recipient = $user;

            try {
                $this->sendSingle(
                    $user,
                    $mailableClass,
                    $mailable,
                    $subject
                );
            } catch (\GuzzleHttp\Exception\ServerException $e) {

                sleep(10);

                $this->sendSingle(
                    $user,
                    $mailableClass,
                    $mailable,
                    $subject
                );

            }
        }
    }

    /**
     * @param User $user
     * @param string $mailableClass
     * @param MyMailable $mailable
     * @param string|null $subject
     */
    private function sendSingle($user, $mailableClass, $mailable, $subject = null)
    {
        \Mail::to($user)
            ->queue(
                $mailable
            );

        event(
            new EmailWasSent(
                $user,
                $mailableClass,
                ($subject) ? get_class($subject) : null,
                ($subject) ? $subject->id : null
            )
        );
    }
}