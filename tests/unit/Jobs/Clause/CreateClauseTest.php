<?php

namespace Tests\Unit\Jobs\Clause;

use App\Events\Licensee\Clause\ClauseWasCreated;
use App\Jobs\Clause\CreateClause;
use App\Models\Clause;
use App\Repositories\Contracts\ClauseRepositoryInterface;
use Tests\TestCase;

/**
 * Class CreateClauseTest
 *
 * @coversBaseClass App\Jobs\Clause\CreateClause
 */
class CreateClauseTest extends TestCase
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @var \App\Repositories\Contracts\ClauseRepositoryInterface
     */
    private $clauseRepo;

    /**
     * @before
     */
    public function setUp()
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();

        $this->clauseRepo = $this->expectsRepository(ClauseRepositoryInterface::class);
    }

    public function test_handle()
    {
        $licenseeId = $this->faker->numberBetween(1, 1000);
        $title = $this->faker->title;
        $body = $this->faker->paragraph;
        $is_default = $this->faker->boolean;

        $clause = factory(Clause::class)->create();
        $this->clauseRepo->shouldReceive('storeForLicensee')
            ->once()
            ->with(
                $licenseeId,
                compact('title', 'body', 'is_default')
            )
            ->andReturn($clause);

        $this->expectsEvents(ClauseWasCreated::class);

        dispatch(
            new CreateClause(
                $licenseeId,
                $title,
                $body,
                $is_default
            )
        );
    }
}