<?php

namespace Tests\Unit\Jobs\Guest;

use App\Events\Licensee\Reservation\GuestWasCreated;
use App\Jobs\Guest\CreateOrGetGuest;
use App\Models\Guest;
use App\Repositories\Contracts\GuestRepositoryInterface;
use Tests\TestCase;

/**
 * Class CreateOrGetGuestTest
 *
 * @coversBaseClass App\Jobs\Guest\CreateOrGetGuest
 */
class CreateOrGetGuestTest extends TestCase
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

    public function test_handle_existing()
    {
        $email = $this->faker->email;
        $attributes = $this->getDummyArray();

        $guest = factory(Guest::class)->make();

        $this->guestRepo->shouldReceive('findWhere')
            ->once()
            ->with(compact('email'))
            ->andReturn($guest);

        $this->doesntExpectEvents(GuestWasCreated::class);

        dispatch(
            new CreateOrGetGuest(
                $email,
                $attributes
            )
        );
    }

    public function test_handle_no_existing()
    {
        $email = $this->faker->email;
        $attributes = $this->getDummyArray();

        $guest = factory(Guest::class)->make();

        $this->guestRepo->shouldReceive('findWhere')
            ->once()
            ->with(compact('email'))
            ->andReturn(null);

        $this->guestRepo->shouldReceive('store')
            ->once()
            ->with(
                collect($attributes)->merge([
                    'email' => $email,
                ])->toArray()
            )->andReturn($guest);

        $this->expectsEvents(GuestWasCreated::class);

        dispatch(
            new CreateOrGetGuest(
                $email,
                $attributes
            )
        );
    }
}