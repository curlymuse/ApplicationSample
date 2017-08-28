<?php

namespace App\Jobs\Guest;

use App\Events\Licensee\Reservation\GuestWasCreated;
use App\Repositories\Contracts\GuestRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateOrGetGuest implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    private $email;

    /**
     * @var array
     */
    private $attributes;

    /**
     * Create a new job instance.
     *
     * @param string $email
     * @param array $attributes
     */
    public function __construct($email, $attributes = [])
    {
        $this->email = $email;
        $this->attributes = $attributes;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(GuestRepositoryInterface $guestRepo)
    {
        $guest = $guestRepo->findWhere([
            'email' => $this->email,
        ]);

        if ($guest) {
            return $guest;
        }

        $guest = $guestRepo->store(
            collect($this->attributes)->merge([
                'email' => $this->email
            ])->toArray()
        );

        event(
            new GuestWasCreated(
                $this->email,
                $this->attributes
            )
        );

        return $guest;
    }
}
