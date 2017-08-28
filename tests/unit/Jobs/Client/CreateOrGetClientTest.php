<?php

namespace Tests\Unit\Jobs\Client;

use App\Events\Licensee\Client\ClientWasCreated;
use App\Jobs\Client\CreateOrGetClient;
use App\Models\Client;
use App\Repositories\Contracts\ClientRepositoryInterface;
use Tests\TestCase;

/**
 * Class CreateOrGetClientTest
 *
 * @coversBaseClass App\Jobs\Client\CreateOrGetClient
 */
class CreateOrGetClientTest extends TestCase
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @var \App\Repositories\Contracts\ClientRepositoryInterface
     */
    private $clientRepo;

    /**
     * @before
     */
    public function setUp()
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();

        $this->clientRepo = $this->expectsRepository(ClientRepositoryInterface::class);
    }

    public function test_handle()
    {
        $placeId = $this->faker->numberBetween(1, 1000);
        $attributes = [];

        $isNewObject = $this->faker->boolean;
        $newObjectResult = false;

        $this->clientRepo->shouldReceive('findOrCreateWithPlaceId')
            ->once()
            ->with(
                $placeId,
                $attributes,
                false
            )
            ->andReturnUsing(function($placeId, $attributes, &$newObjectResult) use ($isNewObject)
            {
                $newObjectResult = $isNewObject;
                return factory(Client::class)->create();
            });

        if ($newObjectResult) {
            $this->expectsEvents(ClientWasCreated::class);
        }

        dispatch(
            new CreateOrGetClient(
                $placeId,
                $attributes
            )
        );
    }
}