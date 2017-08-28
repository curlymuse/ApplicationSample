<?php

namespace Tests\Unit\Controllers\Api\Licensee;

use App\Http\Requests\Guest\StoreGuestRequest;
use App\Http\Requests\Guest\UpdateGuestRequest;
use App\Jobs\Guest\CreateOrGetGuest;
use App\Jobs\Guest\DeleteGuest;
use App\Jobs\Guest\UpdateGuest;
use App\Models\Guest;
use App\Repositories\Contracts\GuestRepositoryInterface;
use App\Transformers\Guest\BasicGuestTransformer;
use App\Transformers\Guest\GuestProfileTransformer;
use Tests\TestCase;
use Tests\Unit\Jobs\Guest\DeleteGuestTest;

/**
 * Class GuestsControllerTest
 *
 * @coversBaseClass \App\Http\Controllers\Api\Licensee\GuestsController
 */
class GuestsControllerTest extends TestCase
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @var \App\Models\Guest
     */
    protected $guest;

    /**
     * @var \App\Repositories\Contracts\GuestRepositoryInterface
     */
    private $guestRepo;

    /**
     * @var \App\Transformers\Guest\GuestProfileTransformer
     */
    private $transformer;

    /**
     * @var \App\Transformers\Guest\BasicGuestTransformer
     */
    private $basicTransformer;

    /**
     * @before
     */
    public function setUp()
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();

        $this->actingAsLicensee();

        $this->guest = factory(Guest::class)->create();

        $this->guestRepo = $this->expectsRepository(GuestRepositoryInterface::class);
        $this->transformer = $this->mock(GuestProfileTransformer::class);
        $this->basicTransformer = $this->mock(BasicGuestTransformer::class);
    }

    /**
     * @covers ::store
     */
    public function test_store()
    {
        $data = [
            'email' => $this->faker->email,
            'attributes'    => $this->getDummyArray(),
        ];

        $this->expectsFormRequest(StoreGuestRequest::class, $data);
        $this->expectsJobWithReturn(CreateOrGetGuest::class, $this->guest);

        $this->basicTransformer->shouldReceive('transform')
            ->once()
            ->with($this->guest)
            ->andReturn($this->guest);

        $this->action('POST', 'Api\Licensee\GuestsController@store');
        $this->assertResponseOk();
    }

    /**
     * @covers ::update
     */
    public function test_update()
    {
        $data = collect($this->getDummyArray())
            ->merge([
                'email' => $this->faker->email,
            ])->toArray();

        $this->expectsFormRequest(UpdateGuestRequest::class, $data);
        $this->expectsJobs(UpdateGuest::class);

        $this->action('PUT', 'Api\Licensee\GuestsController@update', $this->guest->id);
        $this->assertResponseOk();
    }
    
    /**
     * @covers ::destroy
     */
    public function test_destroy()
    {
        $this->expectsJobs(DeleteGuest::class);

        $this->action('DELETE', 'Api\Licensee\GuestsController@destroy', $this->guest->id);
        $this->assertResponseOk();
    }

    /**
     * @covers ::show
     */
    public function test_show()
    {
        $this->guestRepo->shouldReceive('find')
            ->once()
            ->with($this->guest->id)
            ->andReturn($this->guest);

        $this->transformer->shouldReceive('transform')
            ->once()
            ->with($this->guest)
            ->andReturn($this->guest);

        $this->action('GET', 'Api\Licensee\GuestsController@show', $this->guest->id);
        $this->assertResponseOk();
    }
}