<?php

namespace Tests\Integration\Repositories;

use App\Models\Clause;
use App\Models\Licensee;
use App\Repositories\Contracts\ClauseRepositoryInterface;
use Tests\TestCase;

/**
 * Class ClauseRepositoryTest
 *
 * @coversBaseClass \App\Repositories\ClauseRepository
 */
class ClauseRepositoryTest extends TestCase
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @var \App\Repositories\Contracts\ClauseRepositoryInterface
     */
    private $repo;

    /**
     * @before
     */
    public function setUp()
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();

        $this->repo = app(ClauseRepositoryInterface::class);
    }

    /**
     * @covers ::allForLicensee
     */
    public function test_allForLicensee()
    {
        $licensee = factory(Licensee::class)->create();

        $clauses = [];
        for ($i = 0; $i < $this->faker->numberBetween(1, 4); $i++) {
            $clauses[] = factory(Clause::class)->create([
                'licensee_id'   => $licensee->id,
            ]);
        }

        $doNotInclude = factory(Clause::class)->create();

        $getClauses = $this->repo->allForLicensee($licensee->id);

        foreach ($clauses as $clause) {
            $this->assertContains($clause->id, $getClauses->pluck('id'));
        }

        $this->assertNotContains($doNotInclude->id, $getClauses->pluck('id'));
    }

    /**
     * @covers ::storeForLicensee
     */
    public function test_storeForLicensee()
    {
        $licensee = factory(Licensee::class)->create();

        $data = [
            'title'     => $this->faker->title,
            'body'      => $this->faker->paragraph,
            'is_default' => $this->faker->boolean,
        ];

        $this->repo->storeForLicensee($licensee->id, $data);

        $this->seeInDatabase(
            'clauses',
            collect($data)->merge([
                'licensee_id'   => $licensee->id,
            ])->toArray()
        );
    }

    /**
     * @covers ::belongsToLicensee
     */
    public function test_belongsToLicensee()
    {
        $includeClause = factory(Clause::class)->create();
        $excludeClause = factory(Clause::class)->create();

        $this->assertTrue(
            $this->repo->belongsToLicensee(
                $includeClause->id,
                $includeClause->licensee_id
            )
        );

        $this->assertFalse(
            $this->repo->belongsToLicensee(
                $excludeClause->id,
                $includeClause->licensee_id
            )
        );
    }
}