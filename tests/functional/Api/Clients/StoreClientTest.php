<?php

namespace Tests\Functional\Api\Clients;

use App\Models\Client;
use App\Models\EventDateRange;
use App\Models\ProposalRequest;
use App\Models\SpaceRequest;
use Tests\TestCase;

class StoreClientTest extends TestCase
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
        $placeId = str_random();

        $attributes = [
            'name'          => $this->faker->company,
            'address1'      => $this->faker->streetAddress,
            'city'          => $this->faker->city,
            'zip'           => $this->faker->postcode,
        ];
        $data = [
            'attributes'    => $attributes,
        ];

        $this->post(
            sprintf(
                '/api/licensee/clients/%s',
                $placeId
            ),
            $data
        );

        $response = $this->getJsonResponse();

        $this->seeInDatabase(
            'clients',
            collect($attributes)->merge([
                'place_id'  => $placeId,
                'id'    => $response->client_id,
            ])->toArray()
        );
    }
}
