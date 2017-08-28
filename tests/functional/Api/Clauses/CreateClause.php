<?php

namespace Test\Functional\Api\Clauses;

use Tests\TestCase;

class CreateClauseTest extends TestCase
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

        $this->actingAsLicensee();

        $this->faker = \Faker\Factory::create();
    }

    public function test_endpoint()
    {
        $data = [
            'title'      => $this->faker->title,
            'body'      => $this->faker->paragraph,
            'is_default'    => $this->faker->boolean,
        ];

        $this->post(
            '/api/licensee/clauses',
            $data
        );

        $responseObject = $this->getJsonResponse();

        $this->seeInDatabase(
            'clauses',
            collect($data)->merge([
                'licensee_id'   => $this->licensee->id,
                'id'            => $responseObject->clause_id
            ])->toArray()
        );
    }
}
