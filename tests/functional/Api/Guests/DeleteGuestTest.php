<?php

namespace Tests\Functional\Api\Guests;

use App\Models\Guest;
use App\Models\LicenseeTerm;
use App\Models\LicenseeTermGroup;
use Tests\TestCase;

class DeleteGuestTest extends TestCase
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

        $this->delete(
            sprintf(
                '/api/licensee/guests/%d',
                $guest->id
            )
        );

        $this->dontSeeInDatabase(
            'guests',
            $guest->toArray()
        );
    }
}
