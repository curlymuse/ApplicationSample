<?php

namespace Tests\Integration\JobPolicies\ChangeOrder;

use App\Conditions\Contract\ContractOwnerActionMatchesUser;
use App\Exceptions\JobPolicy\UserActionForbiddenException;
use App\Jobs\ChangeOrder\ProcessChangeOrderResponses;
use App\Jobs\Contract\AcceptContract;
use App\Models\ChangeOrder;
use App\Models\Client;
use App\Models\User;
use Tests\TestCase;

/**
 * Class Test
 *
 * @coversBaseClass App\JobPolicies\ChangeOrder\ProcessChangeOrderResponsesPolicy
 */
class ProcessChangeOrderResponsesPolicyTest extends TestCase
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @var \App\Models\ChangeOrder
     */
    protected $changeOrder;

    /**
     * @var array
     */
    protected $changes = [];

    /**
     * @before
     */
    public function setUp()
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();

        $this->changeOrder = factory(ChangeOrder::class)->create();
        $this->user = factory(User::class)->create();
        $this->changes = [];
    }

    public function test_it_succeeds_under_compliant_conditions()
    {
        dispatch(
            new ProcessChangeOrderResponses(
                $this->changeOrder->id,
                $this->user->id,
                $this->changes
            )
        );
    }

    public function test_it_succeeds_under_compliant_conditions_with_client_owned_contract()
    {
        $this->changeOrder->contract->update([
            'is_client_owned'   => true,
            'client_hash'   => str_random(),
        ]);
        $this->changeOrder->update([
            'initiated_by_party'    => 'hotel',
        ]);

        $role = $this->role('client');
        $this->user->roles()->attach(
            $role->id,
            [
                'rolable_type'  => Client::class,
                'rolable_id'    => $this->changeOrder->contract->proposal->proposalRequest->client_id,
            ]
        );

        dispatch(
            new ProcessChangeOrderResponses(
                $this->changeOrder->id,
                $this->user->id,
                $this->changes
            )
        );
    }

    public function test_it_fails_when_non_client_user_responds_as_client_for_client_owned_contract()
    {
        $this->changeOrder->contract->update([
            'is_client_owned'   => true,
            'client_hash'   => str_random(),
        ]);
        $this->changeOrder->update([
            'initiated_by_party'    => 'hotel',
        ]);

        $this->expectsJobPolicyException(
            ProcessChangeOrderResponses::class,
            ContractOwnerActionMatchesUser::class,
            UserActionForbiddenException::class
        );

        dispatch(
            new ProcessChangeOrderResponses(
                $this->changeOrder->id,
                $this->user->id,
                $this->changes
            )
        );
    }
}