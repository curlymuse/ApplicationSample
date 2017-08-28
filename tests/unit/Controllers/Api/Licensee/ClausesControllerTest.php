<?php

namespace Tests\Unit\Controllers\Api\Licensee;

use App\Http\Requests\Clause\CreateClauseRequest;
use App\Http\Requests\Clause\UpdateClauseRequest;
use App\Jobs\Clause\CreateClause;
use App\Jobs\Clause\DeleteClause;
use App\Jobs\Clause\UpdateClause;
use App\Models\Clause;
use App\Repositories\Contracts\ClauseRepositoryInterface;
use App\Transformers\Clause\ClauseTransformer;
use Tests\TestCase;

/**
 * Class ClausesControllerTest
 *
 * @coversBaseClass \App\Http\Controllers\Api\Licensee\ClausesController
 */
class ClausesControllerTest extends TestCase
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @var \App\Models\Clause
     */
    protected $clause;

    /**
     * @var \App\Repositories\Contracts\ClauseRepositoryInterface
     */
    private $clauseRepo;

    /**
     * @var \App\Transformers\Clause\ClauseTransformer
     */
    private $transformer;

    /**
     * @before
     */
    public function setUp()
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();

        $this->clauseRepo = $this->expectsRepository(ClauseRepositoryInterface::class);
        $this->transformer = $this->mock(ClauseTransformer::class);

        $this->actingAsLicensee();

        $this->clause = factory(Clause::class)->create([
            'licensee_id'       => $this->licensee->id,
        ]);
    }

    /**
     * @covers ::store
     */
    public function test_store()
    {
        $data = [
            'title'      => $this->faker->word,
            'body'      => $this->faker->paragraph,
            'is_default'    => $this->faker->boolean,
        ];

        $this->expectsFormRequest(CreateClauseRequest::class, $data);
        $this->expectsJobWithReturn(CreateClause::class, $this->faker->numberBetween(1, 1000));

        $this->action('POST', 'Api\Licensee\ClausesController@store');
        $this->assertResponseOk();
    }

    /**
     * @covers ::index
     */
    public function test_index()
    {
        $dummyCollection = $this->getDummyCollection();

        $this->clauseRepo->shouldReceive('allForLicensee')
            ->once()
            ->with($this->licensee->id)
            ->andReturn($dummyCollection);

        $this->transformer->shouldReceive('transformCollection')
            ->once()
            ->with($dummyCollection)
            ->andReturn($dummyCollection);

        $this->action('GET', 'Api\Licensee\ClausesController@index');
        $this->assertResponseOk();
    }

    /**
     * @covers ::destroy
     */
    public function test_destroy()
    {
        $this->clauseRepo->shouldReceive('belongsToLicensee')
            ->once()
            ->with(
                $this->clause->id,
                $this->licensee->id
            )->andReturn(true);

        $this->expectsJobs(DeleteClause::class);

        $this->action('DELETE', 'Api\Licensee\ClausesController@destroy', $this->clause->id);
        $this->assertResponseOk();
    }

    /**
     * @covers ::update
     */
    public function test_update()
    {
        $data = [
            'attributes'        => $this->getDummyArray(),
        ];

        $this->clauseRepo->shouldReceive('belongsToLicensee')
            ->once()
            ->with(
                $this->clause->id,
                $this->licensee->id
            )->andReturn(true);

        $this->expectsFormRequest(UpdateClauseRequest::class, $data);
        $this->expectsJobs(UpdateClause::class);

        $this->action('PUT', 'Api\Licensee\ClausesController@update', $this->clause->id);
        $this->assertResponseOk();
    }
}