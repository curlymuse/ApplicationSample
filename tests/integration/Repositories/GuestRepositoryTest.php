<?php

namespace Tests\Integration\Repositories;

use App\Models\Guest;
use App\Models\Reservation;
use App\Repositories\Contracts\GuestRepositoryInterface;
use Tests\TestCase;

/**
 * Class GuestRepositoryTest
 *
 * @coversBaseClass \App\Repositories\GuestRepository
 */
class GuestRepositoryTest extends TestCase
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @var \App\Repositories\Contracts\GuestRepositoryInterface
     */
    private $repo;

    /**
     * @before
     */
    public function setUp()
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();

        $this->repo = app(GuestRepositoryInterface::class);
    }

    /**
     * @covers ::addGuestToReservation
     */
    public function test_addGuestToReservation()
    {
        $guest = factory(Guest::class)->create();
        $reservation = factory(Reservation::class)->create();

        $attributes = [
            'is_primary'    => (int)$this->faker->boolean,
            'payment_type'  => $this->faker->randomElement(['RT', 'RTI', 'CC']),
            'notes_to_hotel'    => $this->faker->sentence,
            'notes_internal'    => $this->faker->sentence,
            'special_requests'    => $this->faker->sentence,
        ];

        $this->repo->addGuestToReservation($guest->id, $reservation->id, $attributes);

        $foundGuest = $reservation->guests()->where('guests.id', $guest->id)->first();

        $this->assertNotNull($foundGuest);

        foreach ($attributes as $key => $value) {
            $this->assertEquals($value, $foundGuest->pivot->$key);
        }
    }




























}