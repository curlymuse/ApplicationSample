<?php

namespace Tests\Functional\Api\Contracts;

use App\Models\ChangeOrder;
use App\Models\Contract;
use App\Models\Event;
use App\Models\EventType;
use App\Models\Licensee;
use App\Models\Proposal;
use App\Models\ProposalRequest;
use App\Models\RequestHotel;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class AcceptContractForLicenseeTest extends TestCase
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @var \App\Models\Contract
     */
    protected $contract;

    /**
     * @before
     */
    public function setUp()
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();

        $this->actingAsLicensee();

        $this->contract = factory(Contract::class)->create([
            'proposal_id'   => factory(Proposal::class)->create([
                'proposal_request_id'       => factory(ProposalRequest::class)->create([
                    'event_id'      => factory(Event::class)->create([
                        'licensee_id'       => $this->licensee->id,
                    ])->id
                ])->id
            ])->id
        ]);

        $changeOrder = factory(ChangeOrder::class)->create([
            'contract_id'   => $this->contract->id,
        ]);
        factory(ChangeOrder::class)->create([
            'parent_id' => $changeOrder->id,
            'contract_id'   => $this->contract->id,
            'accepted_at'   => Carbon::now(),
            'accepted_by_user'  => factory(User::class)->create()->id,
        ]);
    }

    public function test_endpoint()
    {
        $data = [
            'signature' => $this->faker->name,
        ];

        $this->post(
            sprintf(
                'api/licensee/contracts/%d/accept',
                $this->contract->id
            ),
            $data
        );

        $this->assertTrue(
            Contract::whereId($this->contract->id)
                ->whereNotNull('accepted_by_owner_at')
                ->whereAcceptedByOwnerUser($this->user->id)
                ->whereAcceptedByOwnerSignature($data['signature'])
                ->exists()
        );
    }
}