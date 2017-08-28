<?php

namespace Tests\Integration\JobPolicies\Contract;

use App\Conditions\Contract\ContractHasChangeOrders;
use App\Conditions\Contract\ContractIsNotOffline;
use App\Conditions\Contract\ContractOwnerActionMatchesUser;
use App\Conditions\Contract\LatestChangeOrderIsFullyAccepted;
use App\Exceptions\JobPolicy\InvalidStateException;
use App\Exceptions\JobPolicy\UserActionForbiddenException;
use App\Jobs\Contract\AcceptContract;
use App\Models\ChangeOrder;
use App\Models\Client;
use App\Models\Contract;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

/**
 * Class Test
 *
 * @coversBaseClass App\JobPolicies\Contract\AcceptContractPolicy
 */
class AcceptContractPolicyTest extends TestCase
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
     * @var \App\Models\User
     */
    protected $user;

    /**
     * @var \App\Models\ChangeOrder
     */
    protected $changeOrder;

    /**
     * @var string
     */
    protected $userType;

    /**
     * @var string
     */
    protected $signature;

    /**
     * @before
     */
    public function setUp()
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();

        //  Basics
        $this->contract = factory(Contract::class)->create();
        $this->user = factory(User::class)->create();
        $this->userType = $this->faker->randomElement(['owner', 'hotel']);
        $this->signature = $this->faker->name;

        //  Completed change order (prerequisite)
        $this->changeOrder = factory(ChangeOrder::class)->create([
            'contract_id'   => $this->contract->id,
        ]);
        factory(ChangeOrder::class)->create([
            'contract_id'   => $this->contract->id,
            'parent_id' => $this->changeOrder->id,
            'accepted_at' => Carbon::now(),
            'accepted_by_user'  => factory(User::class)->create()->id,
        ]);
    }

    public function test_it_succeeds_under_compliant_conditions()
    {
        dispatch(
            new AcceptContract(
                $this->contract->id,
                $this->user->id,
                $this->userType,
                $this->signature
            )
        );
    }

    public function test_it_succeeds_under_compliant_conditions_with_client_owned_contract()
    {
        $this->contract->update([
            'is_client_owned'   => true,
            'client_hash'   => str_random(),
        ]);

        $role = $this->role('client');
        $this->user->roles()->attach(
            $role->id,
            [
                'rolable_type'  => Client::class,
                'rolable_id'    => $this->contract->proposal->proposalRequest->client_id,
            ]
        );

        dispatch(
            new AcceptContract(
                $this->contract->id,
                $this->user->id,
                'owner',
                $this->signature
            )
        );
    }

    public function test_it_fails_with_offline_contract()
    {
        $this->contract->update([
            'is_offline_contract'    => true,
        ]);

        $this->expectsJobPolicyException(
            AcceptContract::class,
            ContractIsNotOffline::class,
            InvalidStateException::class
        );

        dispatch(
            new AcceptContract(
                $this->contract->id,
                $this->user->id,
                'owner',
                $this->signature
            )
        );
    }

    public function test_it_fails_when_non_client_user_accepts_as_client_for_client_owned()
    {
        $this->contract->update([
            'is_client_owned'   => true,
            'client_hash'   => str_random(),
        ]);

        $this->expectsJobPolicyException(
            AcceptContract::class,
            ContractOwnerActionMatchesUser::class,
            UserActionForbiddenException::class
        );

        dispatch(
            new AcceptContract(
                $this->contract->id,
                $this->user->id,
                'owner',
                $this->signature
            )
        );
    }

    public function test_it_fails_when_no_change_orders_present()
    {
        $this->changeOrder->children()->delete();
        $this->changeOrder->delete();

        $this->expectsJobPolicyException(
            AcceptContract::class,
            ContractHasChangeOrders::class,
            InvalidStateException::class
        );

        dispatch(
            new AcceptContract(
                $this->contract->id,
                $this->user->id,
                $this->userType,
                $this->signature
            )
        );
    }

    public function test_it_fails_with_latest_change_order_not_accepted()
    {
        $this->changeOrder->children()->update([
            'accepted_at'   => null,
            'accepted_by_user'  => null,
        ]);

        $this->expectsJobPolicyException(
            AcceptContract::class,
            LatestChangeOrderIsFullyAccepted::class,
            InvalidStateException::class
        );

        dispatch(
            new AcceptContract(
                $this->contract->id,
                $this->user->id,
                $this->userType,
                $this->signature
            )
        );
    }
}