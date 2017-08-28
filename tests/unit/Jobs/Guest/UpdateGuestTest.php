<?php

namespace Tests\Unit\Jobs\Guest;

use App\Events\Licensee\Reservation\GuestWasUpdated;
use App\Jobs\Guest\UpdateGuest;
use App\Repositories\Contracts\GuestRepositoryInterface;
use Tests\TestCase;

/**
 * Class UpdateGuestTest
 *
 * @coversBaseClass App\Jobs\Guest\UpdateGuest
 */
class UpdateGuestTest extends TestCase
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

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
    }

    public function test_handle()
    {
        $attributes = $this->getDummyArray();
        $guestId = $this->faker->numberBetween(1, 1000);

        $this->guestRepo->shouldReceive('update')
            ->once()
            ->with(
                $guestId,
                $attributes
            );

        $this->expectsEvents(GuestWasUpdated::class);

        dispatch(
            new UpdateGuest(
                $guestId,
                $attributes
            )
        );
    }
}