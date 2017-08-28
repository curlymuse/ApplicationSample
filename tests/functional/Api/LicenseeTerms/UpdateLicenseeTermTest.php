<?php

namespace Tests\Functional\Api\LicenseeTerms;

use App\Models\LicenseeTerm;
use App\Models\LicenseeTermGroup;
use Tests\TestCase;

class UpdateLicenseeTermTest extends TestCase
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
            'licensee_id'       => $this->licensee->id,
        ]);
        $term = factory(LicenseeTerm::class)->create([
            'licensee_term_group_id'       => $group->id,
        ]);

        $data = [
            'description'      => $this->faker->paragraph,
            'title'      => $this->faker->word,
        ];

        $this->put(
            sprintf(
                '/api/licensee/term-groups/%d/terms/%d',
                $group->id,
                $term->id
            ),
            $data
        );

        $this->seeInDatabase(
            'licensee_terms',
            [
                'id'        => $term->id,
                'licensee_term_group_id'    => $group->id,
                'description'      => $data['description'],
                'title'      => $data['title'],
            ]
        );
    }
}
