<?php

namespace Tests\Unit\Jobs\Guest;

use App\Events\Licensee\Reservation\GuestWasDeleted;
use App\Jobs\Guest\DeleteGuest;
use App\Repositories\Contracts\GuestRepositoryInterface;
use Tests\TestCase;

/**
 * Class DeleteGuestTest
 *
 * @coversBaseClass App\Jobs\Guest\DeleteGuest
 */
class DeleteGuestTest extends TestCase
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
        $guestId = $this->faker->numberBetween(1, 1000);

        $this->guestRepo->shouldReceive('delete')
            ->once()
            ->with(
                $guestId
            );

        $this->expectsEvents(GuestWasDeleted::class);

        dispatch(
            new DeleteGuest(
                $guestId
            )
        );
    }
}