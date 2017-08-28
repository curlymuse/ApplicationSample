<?php

namespace App\Jobs\Guest;

use App\Events\Licensee\Reservation\GuestWasUpdated;
use App\Repositories\Contracts\GuestRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateGuest implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int
     */
    private $guestId;

    /**
     * @var array
     */
    private $attributes;

    /**
     * Create a new job instance.
     *
     * @param int $guestId
     * @param array $attributes
     */
    public function __construct($guestId, $attributes = [])
    {
        $this->guestId = $guestId;
        $this->attributes = $attributes;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(GuestRepositoryInterface $guestRepo)
    {
        $guestRepo->update(
            $this->guestId,
            $this->attributes
        );

        event(
            new GuestWasUpdated(
                $this->guestId,
                $this->attributes
            )
        );
    }
}
