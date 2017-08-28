<?php

namespace Tests\Unit\Jobs\Guest;

use App\Events\Licensee\Reservation\GuestWasAddedToReservation;
use App\Jobs\Guest\AddGuestToReservation;
use App\Models\Guest;
use App\Models\Reservation;
use App\Repositories\Contracts\GuestRepositoryInterface;
use Tests\TestCase;

/**
 * Class AddGuestToReservationTest
 *
 * @coversBaseClass App\Jobs\Guest\AddGuestToReservation
 */
class AddGuestToReservationTest extends TestCase
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @var \App\Models\Guest
     */
    protected $guest;

    /**
     * @var \App\Models\Reservation
     */
    protected $reservation;

    /**
     * @var \App\Repositories\Contracts\GuestRepositoryInterface
     */
    private $guestRepo;

    /**
     * @before
     */
    public function setUp()
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();

        $this->guestRepo = $this->expectsRepository(GuestRepositoryInterface::class);

        $this->guest = factory(Guest::class)->create();
        $this->reservation = factory(Reservation::class)->create();
    }

    public function test_handle()
    {
        $attributes = $this->getDummyArray();

        $this->guestRepo->shouldReceive('addGuestToReservation')
            ->once()
            ->with(
                $this->guest->id,
                $this->reservation->id,
                $attributes
            );

        $this->expectsEvents(GuestWasAddedToReservation::class);

        dispatch(
            new AddGuestToReservation(
                $this->guest->id,
                $this->reservation->id,
                $attributes
            )
        );
    }
}