<?php

namespace Tests\Unit\Jobs\LicenseeTerm;

use App\Events\Licensee\Terms\LicenseeTermWasUpdated;
use App\Jobs\LicenseeTerm\UpdateLicenseeTerm;
use App\Repositories\Contracts\LicenseeTermRepositoryInterface;
use Tests\TestCase;

/**
 * Class UpdateLicenseeTermTest
 *
 * @coversBaseClass App\Jobs\LicenseeTerm\UpdateLicenseeTerm
 */
class UpdateLicenseeTermTest extends TestCase
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
        $termId = $this->faker->numberBetween(1, 1000);
        $description = $this->faker->paragraph;
        $title = $this->faker->word;

        $this->termRepo->shouldReceive('update')
            ->once()
            ->with(
                $termId,
                compact('description', 'title')
            );

        $this->expectsEvents(LicenseeTermWasUpdated::class);

        dispatch(
            new UpdateLicenseeTerm(
                $termId,
                $title,
                $description
            )
        );
    }
}