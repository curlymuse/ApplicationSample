<?php

namespace Tests\Functional\Api\Guests;

use App\Models\Guest;
use App\Models\LicenseeTerm;
use App\Models\LicenseeTermGroup;
use Tests\TestCase;

class UpdateGuestTest extends TestCase
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

    public function test_endpoint()
    {
        $guest = factory(Guest::class)->create();

        $data = [
            'email'         => $this->faker->randomElement([$this->faker->email, $guest->email]),
            'attributes'    => [
                'name'          => $this->faker->company,
                'address'      => $this->faker->streetAddress,
                'city'          => $this->faker->city,
                'zip'           => $this->faker->postcode,
                'special_requests'  => $this->faker->sentence,
                'notes_to_hotel'  => $this->faker->sentence,
                'notes_internal'  => $this->faker->sentence,
            ]
        ];

        $this->put(
            sprintf(
                '/api/licensee/guests/%d',
                $guest->id
            ),
            $data
        );

        $this->seeInDatabase(
            'guests',
            collect($data['attributes'])->merge([
                'id'    => $guest->id,
                'email' => $data['email'],
            ])->toArray()
        );
    }
}
