<?php

namespace Tests\Unit\Jobs\Client;

use App\Events\Licensee\Client\ClientWasUpdated;
use App\Jobs\Client\UpdateClient;
use App\Repositories\Contracts\ClientRepositoryInterface;
use Tests\TestCase;

/**
 * Class UpdateClientTest
 *
 * @coversBaseClass App\Jobs\Client\UpdateClient
 */
class UpdateClientTest extends TestCase
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
        $placeId = str_random();
        $attributes = $this->getDummyArray();

        $this->clientRepo->shouldReceive('updateWithPlaceId')
            ->once()
            ->with(
                $placeId,
                $attributes
            );

        $this->expectsEvents(ClientWasUpdated::class);

        dispatch(
            new UpdateClient(
                $placeId,
                $attributes
            )
        );
    }
}