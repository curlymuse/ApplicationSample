<?php

namespace Tests\Functional\Api\Clients;

use App\Models\Client;
use App\Models\EventDateRange;
use App\Models\ProposalRequest;
use App\Models\SpaceRequest;
use Tests\TestCase;

class UpdateClientTest extends TestCase
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @var \App\Models\Client
     */
    protected $client;

    /**
     * @before
     */
    public function setUp()
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();

        $this->actingAsLicensee();

        $this->client = factory(Client::class)->create();
    }

    public function test_endpoint()
    {
        $attributes = [
            'name'          => $this->faker->company,
            'address1'      => $this->faker->streetAddress,
            'city'          => $this->faker->city,
            'zip'           => $this->faker->postcode,
        ];
        $data = [
            'attributes'    => $attributes,
        ];

        $this->put(
            sprintf(
                '/api/licensee/clients/%s',
                $this->client->place_id
            ),
            $data
        );

        $this->seeInDatabase(
            'clients',
            collect($attributes)->merge([
                'place_id'  => $this->client->place_id,
                'id'    => $this->client->id,
            ])->toArray()
        );
    }
}
