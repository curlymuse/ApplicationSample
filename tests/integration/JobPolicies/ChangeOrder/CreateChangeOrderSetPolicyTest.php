<?php

namespace Tests\Integration\JobPolicies\ChangeOrder;

use App\Conditions\Contract\ContractHasNoPendingChangeOrders;
use App\Exceptions\JobPolicy\InvalidStateException;
use App\Jobs\ChangeOrder\CreateChangeOrderSet;
use App\Jobs\ChangeOrder\ProcessChangeOrderResponses;
use App\Models\ChangeOrder;
use App\Models\Contract;
use App\Models\User;
use App\Transformers\Contract\ContractTransformer;
use Tests\TestCase;
use Tests\Traits\IncorporatesChangeOrderRequests;

/**
 * Class Test
 *
 * @coversBaseClass App\JobPolicies\ChangeOrder\ProcessChangeOrderResponsesPolicy
 */
class CreateChangeOrderSetPolicyTest extends TestCase
{
    use IncorporatesChangeOrderRequests;

    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @var \App\Models\Contract
     */
    protected $contract;
    
    /**
     * @var array
     */
    protected $addAttachments = [];

    /**
     * @var array
     */
    protected $removeAttachments = [];

    /**
     * @var array
     */
    protected $labels = [];

    /**
     * @var string
     */
    protected $userType;

    /**
     * @before
     */
    public function setUp()
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();

        $this->user = factory(User::class)->create();
        $this->userType = $this->faker->randomElement(['licensee', 'hotel']);
        $this->contract = factory(Contract::class)->create();

        $this->convertContractToInputData();
    }

    public function test_it_succeeds_under_compliant_conditions()
    {
        dispatch(
            new CreateChangeOrderSet(
                $this->contract->id,
                $this->inputData,
                $this->addAttachments,
                $this->removeAttachments,
                $this->user->id,
                $this->userType,
                $this->labels
            )
        );
    }

    public function test_it_fails_when_there_is_a_pending_change_order_on_contract()
    {
        $changeOrder = factory(ChangeOrder::class)->create([
            'contract_id'   => $this->contract->id,
        ]);
        factory(ChangeOrder::class)->create([
            'contract_id'   => $this->contract->id,
            'parent_id'     => $changeOrder->id,
        ]);

        $this->expectsJobPolicyException(
            CreateChangeOrderSet::class,
            ContractHasNoPendingChangeOrders::class,
            InvalidStateException::class
        );

        dispatch(
            new CreateChangeOrderSet(
                $this->contract->id,
                $this->inputData,
                $this->addAttachments,
                $this->removeAttachments,
                $this->user->id,
                $this->userType,
                $this->labels
            )
        );
    }
}