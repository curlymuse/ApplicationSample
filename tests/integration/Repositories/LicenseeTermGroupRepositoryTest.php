<?php

namespace Tests\Integration\Repositories;

use App\Models\Licensee;
use App\Models\LicenseeTermGroup;
use App\Repositories\Contracts\LicenseeTermGroupRepositoryInterface;
use Tests\TestCase;

/**
 * Class LicenseeTermGroupRepositoryTest
 *
 * @coversBaseClass \App\Repositories\LicenseeTermGroupRepository
 */
class LicenseeTermGroupRepositoryTest extends TestCase
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @var \App\Repositories\Contracts\LicenseeTermGroupRepositoryInterface
     */
    private $repo;

    /**
     * @before
     */
    public function setUp()
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();

        $this->repo = app(LicenseeTermGroupRepositoryInterface::class);
    }

    /**
     * @covers ::allForLicensee
     */
    public function test_allForLicensee()
    {
        $includeGroup = factory(LicenseeTermGroup::class)->create();
        $excludeGroup = factory(LicenseeTermGroup::class)->create();

        $groups = $this->repo->allForLicensee($includeGroup->licensee_id);

        $this->assertContains($includeGroup->id, $groups->pluck('id'));
        $this->assertNotContains($excludeGroup->id, $groups->pluck('id'));
    }
    
    /**
     * @covers ::storeForLicensee
     */
    public function test_storeForLicensee()
    {
        $name = $this->faker->name;
        $licensee = factory(Licensee::class)->create();

        $this->repo->storeForLicensee($licensee->id, $name);

        $this->assertTrue(
            $licensee->termGroups()
                ->whereName($name)
                ->exists()
        );
    }

    /**
     * @covers ::belongsToLicensee
     */
    public function test_belongsToLicensee()
    {
        $includeGroup = factory(LicenseeTermGroup::class)->create();
        $excludeGroup = factory(LicenseeTermGroup::class)->create();

        $this->assertTrue(
            $this->repo->belongsToLicensee($includeGroup->id, $includeGroup->licensee_id)
        );

        $this->assertFalse(
            $this->repo->belongsToLicensee($excludeGroup->id, $includeGroup->licensee_id)
        );
    }
}