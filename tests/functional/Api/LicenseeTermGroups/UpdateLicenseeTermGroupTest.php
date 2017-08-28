<?php

namespace Tests\Functional\Api\LicenseeTermGroups;

use App\Models\LicenseeTermGroup;
use Tests\TestCase;

class UpdateLicenseeTermGroupTest extends TestCase
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @before
     */
    public function setUp()
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();

        $this->actingAsLicensee();
    }

    public function test_endpoint()
    {
        $group = factory(LicenseeTermGroup::class)->create([
            'licensee_id'   => $this->licensee->id,
        ]);

        $data = [
            'name'      => $this->faker->name,
        ];

        $this->put(
            sprintf(
                '/api/licensee/term-groups/%d',
                $group->id
            ),
            $data
        );

        $this->seeInDatabase(
            'licensee_term_groups',
            [
                'id'        => $group->id,
                'licensee_id'    => $this->licensee->id,
                'name'      => $data['name'],
            ]
        );
    }
}
