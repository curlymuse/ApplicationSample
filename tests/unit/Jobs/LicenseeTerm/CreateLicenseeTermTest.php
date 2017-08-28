<?php

namespace Tests\Unit\Jobs\LicenseeTerm;

use App\Events\Licensee\Terms\LicenseeTermWasCreated;
use App\Jobs\LicenseeTerm\CreateLicenseeTerm;
use App\Models\LicenseeTerm;
use App\Repositories\Contracts\LicenseeTermRepositoryInterface;
use Tests\TestCase;

/**
 * Class CreateLicenseeTermTest
 *
 * @coversBaseClass App\Jobs\LicenseeTerm\CreateLicenseeTerm
 */
class CreateLicenseeTermTest extends TestCase
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @var \App\Repositories\Contracts\LicenseeTermRepositoryInterface
     */
    private $termRepo;

    /**
     * @before
     */
    public function setUp()
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();

        $this->termRepo = $this->expectsRepository(LicenseeTermRepositoryInterface::class);
    }

    public function test_handle()
    {
        $groupId = $this->faker->numberBetween(1, 1000);
        $title = $this->faker->word;
        $description = $this->faker->paragraph;

        $term = factory(LicenseeTerm::class)->create();

        $this->termRepo->shouldReceive('storeForTermGroup')
            ->once()
            ->with(
                $groupId,
                $title,
                $description
            )
            ->andReturn($term);

        $this->expectsEvents(LicenseeTermWasCreated::class);

        dispatch(
            new CreateLicenseeTerm(
                $groupId,
                $title,
                $description
            )
        );
    }
}