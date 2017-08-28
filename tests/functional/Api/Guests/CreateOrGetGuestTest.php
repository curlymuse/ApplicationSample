<?php

namespace Tests\Functional\Api\Guests;

use App\Models\Client;
use App\Models\EventDateRange;
use App\Models\Guest;
use App\Models\ProposalRequest;
use App\Models\SpaceRequest;
use Tests\TestCase;

class CreateOrGetGuestTest extends TestCase
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @before
     */
    public function setUp()
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();

        $this->actingAsLicensee();
    }

    public function test_endpoint_no_existing()
    {
        $email = $this->faker->email;

        $attributes = [
            'name'          => $this->faker->company,
            'address'      => $this->faker->streetAddress,
            'city'          => $this->faker->city,
            'zip'           => $this->faker->postcode,
            'special_requests'  => $this->faker->sentence,
            'notes_to_hotel'  => $this->faker->sentence,
            'notes_internal'  => $this->faker->sentence,
        ];

        $data = [
            'email'         => $email,
            'attributes'    => $attributes,
        ];

        $this->post(
            sprintf(
                '/api/licensee/guests'
            ),
            $data
        );

        $response = $this->getJsonResponse();

        $this->seeInDatabase(
            'guests',
            collect($attributes)->merge([
                'email' => $email,
                'id'    => $response->guest->id,
            ])->toArray()
        );
    }

    public function test_endpoint_with_existing()
    {
        $guest = factory(Guest::class)->create();
        $email = $guest->email;

        $attributes = [
            'name'          => $this->faker->company,
            'address'      => $this->faker->streetAddress,
            'city'          => $this->faker->city,
            'zip'           => $this->faker->postcode,
            'special_requests'  => $this->faker->sentence,
            'notes_to_hotel'  => $this->faker->sentence,
            'notes_internal'  => $this->faker->sentence,
        ];

        $data = [
            'email'         => $email,
            'attributes'    => $attributes,
        ];

        $this->post(
            sprintf(
                '/api/licensee/guests'
            ),
            $data
        );

        $response = $this->getJsonResponse();

        $this->seeInDatabase(
            'guests',
            collect($guest)->merge([
                'email' => $email,
                'id'    => $response->guest->id,
            ])->toArray()
        );
    }
}
