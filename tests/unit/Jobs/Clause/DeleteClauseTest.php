<?php

namespace Tests\Unit\Jobs\Clause;

use App\Events\Licensee\Clause\ClauseWasDeleted;
use App\Jobs\Clause\DeleteClause;
use App\Repositories\Contracts\ClauseRepositoryInterface;
use Tests\TestCase;

/**
 * Class DeleteClauseTest
 *
 * @coversBaseClass App\Jobs\Clause\DeleteClause
 */
class DeleteClauseTest extends TestCase
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

        $this->clauseRepo->shouldReceive('delete')
            ->once()
            ->with($clauseId);

        $this->expectsEvents(ClauseWasDeleted::class);

        dispatch(
            new DeleteClause(
                $clauseId
            )
        );
    }
}