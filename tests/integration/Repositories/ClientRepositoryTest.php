<?php

namespace Tests\Integration\Repositories;

use App\Models\Client;
use App\Models\Event;
use App\Repositories\Contracts\ClientRepositoryInterface;
use Tests\TestCase;

/**
 * Class ClientRepositoryTest
 *
 * @coversBaseClass \App\Repositories\ClientRepository
 */
class ClientRepositoryTest extends TestCase
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @var \App\Repositories\Contracts\ClientRepositoryInterface
     */
    private $repo;

    /**
     * @before
     */
    public function setUp()
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();

        $this->repo = app(ClientRepositoryInterface::class);
    }
    
    /**
     * @covers ::findOrCreateWithPlaceId
     */
    public function test_findOrCreateWithPlaceId()
    {
        //  CASE #1: Client already exists
        $client = factory(Client::class)->create();

        $foundClient = $this->repo->findOrCreateWithPlaceId($client->place_id, []);
        $this->assertEquals($client->id, $foundClient->id);

        //  CASE #2: Create a client
        $placeId = $this->faker->word;
        $attributes = [
            'name'          => $this->faker->company,
            'address1'      => $this->faker->streetAddress,
            'city'          => $this->faker->city,
            'zip'           => $this->faker->postcode,
        ];

        $this->repo->findOrCreateWithPlaceId($placeId, $attributes);

        $search = collect($attributes)
            ->merge([
                'place_id'     => $placeId,
            ])->toArray();

        $this->assertTrue(\App\Models\Client::where($search)->exists());
    }

    /**
     * @covers ::updateWithPlaceId
     */
    public function test_updateWithPlaceId()
    {
        $client = factory(Client::class)->create();

        $attributes = [
            'name'          => $this->faker->company,
            'address1'      => $this->faker->streetAddress,
            'city'          => $this->faker->city,
            'zip'           => $this->faker->postcode,
        ];

        $this->repo->updateWithPlaceId($client->place_id, $attributes);

        $this->seeInDatabase(
            'clients',
            collect($attributes)->merge([
                'place_id'  => $client->place_id,
            ])->toArray()
        );
    }

    /**
     * @covers ::allForLicensee
     */
    public function test_allForLicensee()
    {
        $event = factory(Event::class)->create();
        $excludeClient = factory(Client::class)->create();

        $clients = $this->repo->allForLicensee($event->licensee_id);

        $this->assertContains($event->client_id, $clients->pluck('id'));
        $this->assertNotContains($excludeClient->id, $clients->pluck('id'));
    }
}