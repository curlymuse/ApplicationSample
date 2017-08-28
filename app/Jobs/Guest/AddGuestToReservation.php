<?php

namespace App\Jobs\Guest;

use App\Events\Licensee\Reservation\GuestWasAddedToReservation;
use App\Repositories\Contracts\GuestRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AddGuestToReservation implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int
     */
    private $guestId;

    /**
     * @var int
     */
    private $reservationId;

    /**
     * @var array
     */
    private $attributes;

    /**
     * Create a new job instance.
     *
     * @param int $guestId
     * @param int $reservationId
     * @param array $attributes
     */
    public function __construct($guestId, $reservationId, $attributes = [])
    {
        $this->guestId = $guestId;
        $this->reservationId = $reservationId;
        $this->attributes = $attributes;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(GuestRepositoryInterface $guestRepo)
    {
        $guestRepo->addGuestToReservation(
            $this->guestId,
            $this->reservationId,
            $this->attributes
        );

        event(
            new GuestWasAddedToReservation(
                $this->guestId,
                $this->reservationId,
                $this->attributes
            )
        );
    }
}
