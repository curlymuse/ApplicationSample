<?php

namespace Tests\Integration\Repositories;

use App\Models\Brand;
use App\Models\ChangeOrder;
use App\Models\EventDateRange;
use App\Models\Contract;
use App\Models\Hotel;
use App\Models\PaymentMethod;
use App\Models\Proposal;
use App\Models\ProposalDateRange;
use App\Models\ProposalRequest;
use App\Models\RequestHotel;
use App\Models\ReservationMethod;
use App\Models\SpaceRequest;
use App\Models\User;
use App\Repositories\Contracts\ContractRepositoryInterface;
use Carbon\Carbon;
use Tests\TestCase;

/**
 * Class ContractRepositoryTest
 *
 * @coversBaseClass \App\Repositories\ContractRepository
 */
class ContractRepositoryTest extends TestCase
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @var \App\Repositories\Contracts\ContractRepositoryInterface
     */
    private $repo;

    /**
     * @before
     */
    public function setUp()
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();

        $this->repo = app(ContractRepositoryInterface::class);
    }

    /**
     * @covers ::allForProposalRequest
     */
    public function test_allForProposalRequest()
    {
        $includeContract = factory(Contract::class)->create();
        $excludeContract = factory(Contract::class)->create();

        $contracts = $this->repo->allForProposalRequest(
            $includeContract->proposal->proposalRequest->id
        );

        $this->assertContains($includeContract->id, $contracts->pluck('id'));
        $this->assertNotContains($excludeContract->id, $contracts->pluck('id'));
    }

    /**
     * @covers ::allForLicensee
     */
    public function test_allForLicensee()
    {
        $includeContract = factory(Contract::class)->create();
        $excludeContract = factory(Contract::class)->create();

        $contracts = $this->repo->allForLicensee(
            $includeContract->proposal->proposalRequest->event->licensee_id
        );

        $this->assertContains($includeContract->id, $contracts->pluck('id'));
        $this->assertNotContains($excludeContract->id, $contracts->pluck('id'));
    }

    /**
     * @covers ::allForHotelier
     */
    public function test_allForHotelier()
    {
        $user = factory(User::class)->create();

        $contractIds = [];
        for ($i = 0; $i < $this->faker->numberBetween(1, 3); $i++) {
            $hotel = factory(Hotel::class)->create();
            $hotel->hoteliers()->attach($user->id);
            $contract = factory(Contract::class)->create([
                'proposal_id'   => factory(Proposal::class)->create([
                    'hotel_id'  => $hotel->id,
                ])->id,
            ]);
            $contractIds[] = $contract->id;
        }

        $excludeContract = factory(Contract::class)->create();

        $contracts = $this->repo->allForHotelier($user->id);

        foreach ($contractIds as $id) {
            $this->assertContains($id, $contracts->pluck('id'));
        }

        $this->assertNotContains($excludeContract->id, $contracts->pluck('id'));
    }

    /**
     * @covers ::allForHotelier
     */
    public function test_allForHotelier_with_proposal_request_id()
    {
        $includeContract = factory(Contract::class)->create();
        $excludeContract = factory(Contract::class)->create();
        $user = factory(User::class)->create();

        foreach ([$includeContract, $excludeContract] as $contract) {
            $contract->proposal->hotel->hoteliers()->attach(
                $user->id
            );
        }

        $contracts = $this->repo->allForHotelier($user->id, null, null, $includeContract->proposal->proposal_request_id);

        $this->assertContains($includeContract->id, $contracts->pluck('id'));
        $this->assertNotContains($excludeContract->id, $contracts->pluck('id'));
    }

    /**
     * @covers ::allForGSO
     */
    public function test_allForGSO()
    {
        $user = factory(User::class)->create();
        $brand = factory(Brand::class)->create();
        $role = $this->role('gso');
        $user->roles()->attach(
            $role->id,
            [
                'rolable_type'  => Brand::class,
                'rolable_id'    => $brand->id,
            ]
        );

        $contractIds = [];
        for ($i = 0; $i < $this->faker->numberBetween(1, 3); $i++) {
            $hotel = factory(Hotel::class)->create(['brand_id'  => $brand->id]);
            $contract = factory(Contract::class)->create([
                'proposal_id'   => factory(Proposal::class)->create([
                    'hotel_id'      => $hotel->id,
                ])->id,
            ]);
            $contractIds[] = $contract->id;
        }

        $excludeContract = factory(Contract::class)->create();

        $contracts = $this->repo->allForGSO($user->id);

        foreach ($contractIds as $id) {
            $this->assertContains($id, $contracts->pluck('id'));
        }

        $this->assertNotContains($excludeContract->id, $contracts->pluck('id'));
    }

    /**
     * @covers ::allForGSO_with_proposal_request_id
     */
    public function test_allForGSO_with_proposal_request_id()
    {
        $user = factory(User::class)->create();
        $brand = factory(Brand::class)->create();
        $role = $this->role('gso');
        $user->roles()->attach(
            $role->id,
            [
                'rolable_type'  => Brand::class,
                'rolable_id'    => $brand->id,
            ]
        );
        $hotel = factory(Hotel::class)->create([
            'brand_id'  => $brand->id,
        ]);

        $includeContract = factory(Contract::class)->create([
            'proposal_id'   => factory(Proposal::class)->create([
                'hotel_id'  => $hotel->id,
            ])->id
        ]);
        $excludeContract = factory(Contract::class)->create([
            'proposal_id'   => factory(Proposal::class)->create([
                'hotel_id'  => $hotel->id,
            ])->id
        ]);

        $contracts = $this->repo->allForGSO($user->id, null, null, $includeContract->proposal->proposal_request_id);

        $this->assertContains($includeContract->id, $contracts->pluck('id'));
        $this->assertNotContains($excludeContract->id, $contracts->pluck('id'));
    }

    /**
     * @covers ::hasChangeOrders
     */
    public function test_hasChangeOrders()
    {
        $includeContract = factory(Contract::class)->create();
        $excludeContract = factory(Contract::class)->create();

        factory(ChangeOrder::class)->create([
            'contract_id'   => $includeContract->id,
        ]);

        $this->assertTrue(
            $this->repo->hasChangeOrders($includeContract->id)
        );
        $this->assertFalse(
            $this->repo->hasChangeOrders($excludeContract->id)
        );
    }

    /**
     * @covers ::hasLatestChangeOrderSetAccepted
     */
    public function test_hasLatestChangeOrderSetAccepted()
    {
        $includeContract = factory(Contract::class)->create();
        $excludeContract = factory(Contract::class)->create();

        $acceptedChangeOrder = factory(ChangeOrder::class)->create([
            'contract_id'   => $includeContract->id,
        ]);
        $acceptedChangeOrder->children()->save(factory(ChangeOrder::class)->create([
            'contract_id'   => $includeContract->id,
            'accepted_at'   => Carbon::now(),
            'accepted_by_user'  => factory(User::class)->create()->id,
        ]));

        $notAcceptedChangeOrder = factory(ChangeOrder::class)->create([
            'contract_id'   => $excludeContract->id,
        ]);
        $notAcceptedChangeOrder->children()->save(factory(ChangeOrder::class)->create([
            'contract_id'   => $excludeContract->id,
        ]));

        $this->assertTrue(
            $this->repo->hasLatestChangeOrderSetAccepted($includeContract->id)
        );
        $this->assertFalse(
            $this->repo->hasLatestChangeOrderSetAccepted($excludeContract->id)
        );
    }

    /**
     * @covers ::validateClientHash
     */
    public function test_validateClientHash()
    {
        $contract = factory(Contract::class)->create([
            'client_hash'   => str_random(),
            'is_client_owned'   => true,
        ]);

        //  Passing case
        $this->assertTrue(
            $this->repo->validateClientHash($contract->id, $contract->client_hash)
        );

        //  Failure due to wrong hash string
        $this->assertFalse(
            $this->repo->validateClientHash($contract->id, $contract->client_hash . str_random())
        );

        //  Failure due to is_client_owned being false
        $contract->update([
            'is_client_owned'   => false,
        ]);
        $this->assertFalse(
            $this->repo->validateClientHash($contract->id, $contract->client_hash)
        );
    }

    /**
     * @covers ::hasPendingChangeOrders
     */
    public function test_hasPendingChangeOrders()
    {
        //  This contract has a pending change order
        $includeContract = factory(Contract::class)->create();
        $includeChangeOrderSet = factory(ChangeOrder::class)->create([
            'contract_id'   => $includeContract->id,
        ]);
        factory(ChangeOrder::class)->create([
            'contract_id'   => $includeContract->id,
            'parent_id' => $includeChangeOrderSet->id,
        ]);

        //  This contract has no change orders
        $excludeContract1 = factory(Contract::class)->create();

        //  This contract has change orders, but they are not pending
        $excludeContract2 = factory(Contract::class)->create();
        $excludeChangeOrderSet = factory(ChangeOrder::class)->create([
            'contract_id'       => $excludeContract2->id,
        ]);
        factory(ChangeOrder::class)->create([
            'contract_id'   => $excludeContract2->id,
            'parent_id'     => $excludeChangeOrderSet->id,
            $this->faker->randomElement(['declined_at', 'accepted_at']) => Carbon::now(),
        ]);

        $this->assertTrue(
            $this->repo->hasPendingChangeOrders($includeContract->id)
        );
        $this->assertFalse(
            $this->repo->hasPendingChangeOrders($excludeContract1->id)
        );
        $this->assertFalse(
            $this->repo->hasPendingChangeOrders($excludeContract2->id)
        );
    }

    /**
     * @covers ::initializeWithProposal
     */
    public function test_initializeWithProposal()
    {
        $proposal = factory(Proposal::class)->create();

        $dateRangeData = factory(ProposalDateRange::class)->create([
            'proposal_id' => $proposal->id
        ]);
        $reservationMethod = factory(ReservationMethod::class)->create();
        $paymentMethod = factory(PaymentMethod::class)->create();

        $proposal->proposalRequest->reservationMethods()->attach($reservationMethod->id);
        $proposal->proposalRequest->paymentMethods()->attach($paymentMethod->id);

        $eventDateRangeId = $dateRangeData->event_date_range_id;

        $contract = $this->repo->initializeWithProposal($proposal, $eventDateRangeId);

        $transferFromSpaceRequest = [
            'start_time',
            'end_time',
            'name',
            'attendees',
            'budget',
            'budget_units',
            'room_type',
            'layout',
            'requests',
            'equipment',
            'meal',
            'notes',
        ];

        $expectedMeetingSpaceData = json_decode($dateRangeData->meeting_spaces);
        foreach ($expectedMeetingSpaceData as &$item) {
            $spaceRequest = SpaceRequest::find($item->id);
            foreach ($transferFromSpaceRequest as $field) {
                $item->$field = $spaceRequest->$field;
            }
        }

        $expectedFBData = json_decode($dateRangeData->food_and_beverage_spaces);
        foreach ($expectedFBData as &$item) {
            $spaceRequest = SpaceRequest::find($item->id);
            foreach ($transferFromSpaceRequest as $field) {
                $item->$field = $spaceRequest->$field;
            }
        }

        $checkData = collect($proposal)->only([
            'attrition_rate',
            'commission',
            'rebate',
            'additional_charge_per_adult',
            'tax_rate',
            'min_age_to_check_in',
            'min_length_of_stay',
            'additional_fees',
            'additional_fees_units',
            'deposit_policy',
            'cancellation_policy',
            'cancellation_policy_days',
            'cancellation_policy_file',
            'notes',
            'questions',
        ])->merge([
            'proposal_id'       => $proposal->id,
            'is_meeting_space_required' => $proposal->proposalRequest->is_meeting_space_required,
            'is_food_and_beverage_required' => $proposal->proposalRequest->is_food_and_beverage_required,
            'event_date_range_id'   => $eventDateRangeId,
            'meeting_spaces'    => json_encode($expectedMeetingSpaceData),
            'food_and_beverage'  => json_encode($expectedFBData),
        ])->toArray();

        $this->seeInDatabase('contracts', $checkData);

        $this->assertContains($reservationMethod->id, $contract->reservationMethods()->pluck('reservation_methods.id'));
        $this->assertContains($paymentMethod->id, $contract->paymentMethods()->pluck('payment_methods.id'));
    }

    /**
     * @covers ::isClientOwned
     */
    public function test_isClientOwned()
    {
        $clientOwnedContract = factory(Contract::class)->create([
            'is_client_owned'   => true,
            'client_hash'   => str_random(),
        ]);
        $licenseeOwnedContract = factory(Contract::class)->create();

        $this->assertTrue($this->repo->isClientOwned($clientOwnedContract->id));
        $this->assertFalse($this->repo->isClientOwned($licenseeOwnedContract->id));
    }

    /**
     * @covers ::isOffline
     */
    public function test_isOffline()
    {
        $offlineContract = factory(Contract::class)->create([
            'is_offline_contract'    => true,
        ]);
        $onlineContract = factory(Contract::class)->create();

        $this->assertTrue($this->repo->isOffline($offlineContract->id));
        $this->assertFalse($this->repo->isOffline($onlineContract->id));
    }

    /**
     * @covers ::removeReservationMethod
     */
    public function test_removeReservationMethod()
    {
        $contract = factory(Contract::class)->create();
        $method = factory(ReservationMethod::class)->create();
        $contract->reservationMethods()->attach(
            $method->id
        );

        $this->repo->removeReservationMethod($contract->id, $method->id);

        $this->dontSeeInDatabase(
            'contract_reservation_method',
            [
                'contract_id'   => $contract->id,
                'reservation_method_id'     => $method->id,
            ]
        );
    }

    /**
     * @covers ::removePaymentMethod
     */
    public function test_removePaymentMethod()
    {
        $contract = factory(Contract::class)->create();
        $method = factory(PaymentMethod::class)->create();
        $contract->paymentMethods()->attach(
            $method->id
        );

        $this->repo->removePaymentMethod($contract->id, $method->id);

        $this->dontSeeInDatabase(
            'contract_payment_method',
            [
                'contract_id'   => $contract->id,
                'payment_method_id'     => $method->id,
            ]
        );
    }

    /**
     * @covers ::addReservationMethod
     */
    public function test_addReservationMethod()
    {
        $contract = factory(Contract::class)->create();
        $method = factory(ReservationMethod::class)->create();

        $this->repo->addReservationMethod($contract->id, $method->id);

        $this->seeInDatabase(
            'contract_reservation_method',
            [
                'contract_id'   => $contract->id,
                'reservation_method_id'     => $method->id,
            ]
        );
    }

    /**
     * @covers ::addPaymentMethod
     */
    public function test_addPaymentMethod()
    {
        $contract = factory(Contract::class)->create();
        $method = factory(PaymentMethod::class)->create();

        $this->repo->addPaymentMethod($contract->id, $method->id);

        $this->seeInDatabase(
            'contract_payment_method',
            [
                'contract_id'   => $contract->id,
                'payment_method_id'     => $method->id,
            ]
        );
    }

    /**
     * @covers ::makeOffline
     */
    public function test_makeOffline()
    {
        $contract = factory(Contract::class)->create();

        $this->repo->makeOffline($contract->id);

        $this->seeInDatabase(
            'contracts',
            [
                'id'    => $contract->id,
                'is_offline_contract'   => true,
            ]
        );
    }

    /**
     * @covers ::resetDeclineForOwner
     */
    public function test_resetDeclineForOwner()
    {
        $contract = factory(Contract::class)->create();
        $contract->update([
            'declined_by_owner_user' => factory(User::class)->create()->id,
            'declined_by_owner_at'   => $this->faker->dateTime,
            'declined_by_owner_because'  => $this->faker->sentence,
        ]);

        $this->repo->resetDeclineForOwner($contract->id);

        $this->assertTrue(
            Contract::whereId($contract->id)
                ->whereNull('declined_by_owner_user')
                ->whereNull('declined_by_owner_at')
                ->whereNull('declined_by_owner_because')
                ->exists()
        );
    }

    /**
     * @covers ::declineForOwner
     */
    public function test_declineForOwner()
    {
        $contract = factory(Contract::class)->create();
        $user = factory(User::class)->create();
        $reason = $this->faker->sentence;

        $this->repo->declineForOwner(
            $contract->id,
            $user->id,
            $reason
        );

        $this->assertTrue(
            Contract::whereNotNull('declined_by_owner_at')
                ->whereDeclinedByOwnerUser($user->id)
                ->whereDeclinedByOwnerBecause($reason)
                ->whereNotNull('declined_by_owner_at')
                ->whereId($contract->id)
                ->exists()
        );
    }

    /**
     * @covers ::declineForHotel
     */
    public function test_declineForHotel()
    {
        $contract = factory(Contract::class)->create();
        $reason = $this->faker->sentence;
        $user = factory(User::class)->create();

        $this->repo->declineForHotel(
            $contract->id,
            $user->id,
            $reason
        );

        $this->assertTrue(
            Contract::whereNotNull('declined_by_hotel_at')
                ->whereDeclinedByHotelUser($user->id)
                ->whereDeclinedByHotelBecause($reason)
                ->whereNotNull('declined_by_hotel_at')
                ->whereId($contract->id)
                ->exists()
        );
    }

    /**
     * @covers ::acceptForHotel
     */
    public function test_acceptForHotel()
    {
        $contract = factory(Contract::class)->create();
        $user = factory(User::class)->create();
        $signature = $this->faker->name;

        $this->repo->acceptForHotel(
            $contract->id,
            $user->id,
            $signature
        );

        $this->assertTrue(
            Contract::whereNotNull('accepted_by_hotel_at')
                ->whereAcceptedByHotelUser($user->id)
                ->whereNotNull('accepted_by_hotel_at')
                ->whereAcceptedByHotelSignature($signature)
                ->whereId($contract->id)
                ->exists()
        );
    }

    /**
     * @covers ::acceptForOwner
     */
    public function test_acceptForOwner()
    {
        $contract = factory(Contract::class)->create();
        $user = factory(User::class)->create();
        $signature = $this->faker->name;

        $this->repo->acceptForOwner(
            $contract->id,
            $user->id,
            $signature
        );

        $this->assertTrue(
            Contract::whereNotNull('accepted_by_owner_at')
                ->whereAcceptedByOwnerUser($user->id)
                ->whereAcceptedByOwnerSignature($signature)
                ->whereNotNull('accepted_by_owner_at')
                ->whereId($contract->id)
                ->exists()
        );
    }

    /**
     * @covers ::revokeClientOwnership
     */
    public function test_revokeClientOwnership()
    {
        $contract = factory(Contract::class)->create([
            'is_client_owned'   => true,
            'client_hash'   => str_random(),
        ]);

        $this->repo->revokeClientOwnership($contract->id);

        $this->seeInDatabase(
            'contracts',
            [
                'id'    => $contract->id,
                'is_client_owned'   => false,
                'client_hash'   => null,
            ]
        );
    }

    /**
     * @covers ::transferOwnershipToClient
     */
    public function test_transferOwnershipToClient()
    {
        $contract = factory(Contract::class)->create();

        $this->repo->transferOwnershipToClient($contract->id);

        $this->assertTrue(
            Contract::whereId($contract->id)
                ->whereIsClientOwned(true)
                ->whereNotNull('client_hash')
                ->exists()
        );
    }

    /**
     * @covers ::userBelongsToHotelOnContract
     */
    public function test_userBelongsToHotelOnContract_gso()
    {
        $includeUser = factory(User::class)->create();
        $excludeUser = factory(User::class)->create();

        $contract = factory(Contract::class)->create();

        $role = $this->role('hotelso');
        $includeUser->roles()->attach(
            $role->id,
            [
                'rolable_type'  => Brand::class,
                'rolable_id'    => $contract->proposal->hotel->brand_id
            ]
        );

        $this->assertTrue(
            $this->repo->userBelongsToHotelOnContract($includeUser->id, $contract->id)
        );

        $this->assertFalse(
            $this->repo->userBelongsToHotelOnContract($excludeUser->id, $contract->id)
        );
    }

    /**
     * @covers ::userBelongsToHotelOnContract
     */
    public function test_userBelongsToHotelOnContract_hotelier()
    {
        $includeUser = factory(User::class)->create();
        $excludeUser = factory(User::class)->create();

        $contract = factory(Contract::class)->create();

        $role = $this->role('hotelier');
        $includeUser->roles()->attach(
            $role->id,
            [
                'rolable_type'  => Hotel::class,
                'rolable_id'    => $contract->proposal->hotel_id
            ]
        );
        $contract->proposal->hotel->hoteliers()->attach($includeUser->id);

        $this->assertTrue(
            $this->repo->userBelongsToHotelOnContract($includeUser->id, $contract->id)
        );

        $this->assertFalse(
            $this->repo->userBelongsToHotelOnContract($excludeUser->id, $contract->id)
        );
    }

    /**
     * @covers ::belongsToProposalRequest
     */
    public function test_belongsToProposalRequest()
    {
        $contract = factory(Contract::class)->create();

        $this->assertTrue(
            $this->repo->belongsToProposalRequest(
                $contract->id,
                $contract->proposal->proposal_request_id
            )
        );

        $this->assertFalse(
            $this->repo->belongsToProposalRequest(
                $contract->id,
                factory(ProposalRequest::class)->create()->id
            )
        );
    }
    
    /**
     * @covers ::belongsToLicensee
     */
    public function test_belongsToLicensee()
    {
        $includeContract = factory(Contract::class)->create();
        $excludeContract = factory(Contract::class)->create();

        $this->assertTrue(
            $this->repo->belongsToLicensee(
                $includeContract->id,
                $includeContract->proposal->proposalRequest->event->licensee_id
            )
        );

        $this->assertFalse(
            $this->repo->belongsToLicensee(
                $excludeContract->id,
                $includeContract->proposal->proposalRequest->event->licensee_id
            )
        );
    }

    /**
     * @covers ::belongsToHotel
     */
    public function test_belongsToHotel()
    {
        $includeContract = factory(Contract::class)->create();
        $excludeContract = factory(Contract::class)->create();

        $this->assertTrue(
            $this->repo->belongsToHotel(
                $includeContract->id,
                $includeContract->proposal->hotel_id
            )
        );

        $this->assertFalse(
            $this->repo->belongsToLicensee(
                $excludeContract->id,
                $includeContract->proposal->hotel_id
            )
        );
    }
}
