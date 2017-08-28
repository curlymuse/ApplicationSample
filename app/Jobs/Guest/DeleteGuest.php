<?php

namespace App\Jobs\Guest;

use App\Events\Licensee\Reservation\GuestWasDeleted;
use App\Repositories\Contracts\GuestRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class DeleteGuest implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int
     */
    private $guestId;

    /**
     * Create a new job instance.
     *
     * @param int $guestId
     */
    public function __construct($guestId)
    {
        $this->guestId = $guestId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(GuestRepositoryInterface $guestRepo)
    {
        $guestRepo->delete($this->guestId);

        event(
            new GuestWasDeleted(
                $this->guestId
            )
        );
    }
}
