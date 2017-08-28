<?php

namespace Tests\Functional\Api\LicenseeTerm;

use App\Models\LicenseeTermGroup;
use Tests\TestCase;

class StoreLicenseeTermTest extends TestCase
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
        //  To prevent licensee and group from having same ID (fixing bug)
        factory(LicenseeTermGroup::class)->create();
        $group = factory(LicenseeTermGroup::class)->create([
            'licensee_id'       => $this->licensee->id,
        ]);

        $data = [
            'description'      => $this->faker->paragraph,
            'title'      => $this->faker->title,
        ];

        $this->post(
            sprintf(
                '/api/licensee/term-groups/%d/terms',
                $group->id
            ),
            $data
        );

        $response = $this->getJsonResponse();

        $this->seeInDatabase(
            'licensee_terms',
            [
                'id'        => $response->term_id,
                'licensee_term_group_id'    => $group->id,
                'description'      => $data['description'],
                'title'      => $data['title'],
            ]
        );
    }
}
