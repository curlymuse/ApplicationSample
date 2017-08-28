<?php

namespace Tests\Unit\Controllers\Api\Licensee;

use App\Http\Requests\Client\StoreClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Jobs\Client\CreateOrGetClient;
use App\Jobs\Client\UpdateClient;
use App\Repositories\Contracts\ClientRepositoryInterface;
use App\Transformers\Client\ClientTransformer;
use Tests\TestCase;
use Tests\Unit\Jobs\Client\UpdateClientTest;

/**
 * Class ClientsControllerTest
 *
 * @coversBaseClass \App\Http\Controllers\Api\Licensee\ClientsController
 */
class ClientsControllerTest extends TestCase
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
     * @var \App\Transformers\Client\ClientTransformer
     */
    private $transformer;

    /**
     * @before
     */
    public function setUp()
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();

        $this->actingAsLicensee();

        $this->clientRepo = $this->expectsRepository(ClientRepositoryInterface::class);
        $this->transformer = $this->mock(ClientTransformer::class);
    }

    /**
     * @covers ::store
     */
    public function test_store()
    {
        $googleId = $this->faker->uuid;
        $data = [
            'attributes'    => $this->getDummyArray(),
        ];

        $this->expectsFormRequest(StoreClientRequest::class, $data);

        $id = $this->faker->numberBetween(1, 1000);
        $this->expectsJobWithReturn(CreateOrGetClient::class, $id);

        $this->action('POST', 'Api\Licensee\ClientsController@store', $googleId);
        $this->assertJsonObject(['client_id' => $id]);
    }

    /**
     * @covers ::update
     */
    public function test_update()
    {
        $googleId = $this->faker->uuid;
        $data = [
            'attributes'    => $this->getDummyArray(),
        ];

        $this->expectsFormRequest(UpdateClientRequest::class, $data);
        $this->expectsJobs(UpdateClient::class);

        $this->action('PUT', 'Api\Licensee\ClientsController@update', $googleId);
        $this->assertResponseOk();
    }

    /**
     * @covers ::index
     */
    public function test_index()
    {
        $dummyCollection = $this->getDummyCollection();

        $this->clientRepo->shouldReceive('allForLicensee')
            ->once()
            ->with($this->licensee->id)
            ->andReturn($dummyCollection);

        $this->transformer->shouldReceive('transformCollection')
            ->once()
            ->with($dummyCollection)
            ->andReturn($dummyCollection);

        $this->action('GET', 'Api\Licensee\ClientsController@index');
        $this->assertResponseOk();
    }
}