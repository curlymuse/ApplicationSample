<?php

namespace Tests\Unit\Jobs\Clause;

use App\Events\Licensee\Clause\ClauseWasUpdated;
use App\Jobs\Clause\UpdateClause;
use App\Repositories\Contracts\ClauseRepositoryInterface;
use Tests\TestCase;

/**
 * Class UpdateClauseTest
 *
 * @coversBaseClass App\Jobs\Clause\UpdateClause
 */
class UpdateClauseTest extends TestCase
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
        $clauseId = $this->faker->numberBetween(1, 1000);
        $attributes = $this->getDummyArray();

        $this->clauseRepo->shouldReceive('update')
            ->once()
            ->with(
                $clauseId,
                $attributes
            );

        $this->expectsEvents(ClauseWasUpdated::class);

        dispatch(
            new UpdateClause(
                $clauseId,
                $attributes
            )
        );
    }
}