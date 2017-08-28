<?php

namespace Tests;

use \AcceptanceTester;
use \Faker\Factory as Faker;
// ref: https://facebook.github.io/php-webdriver/classes/WebDriverElement.html
use \Facebook\WebDriver\WebDriverElement;
use Carbon\Carbon;

class AcceptanceTestCase
{
    protected $faker;

    protected $acceptanceTester;

    /**
     * User
     *
     * @var \App\Models\User
     */
    protected $user;

    /**
     * Licensee
     *
     * @var \App\Models\Licensee
     */
    protected $licensee;

    /**
     * Hotelier
     *
     * @var \App\Models\Hotel
     */
    protected $hotel;

    /**
     * Default room names
     *
     * @var array
     */
    protected $roomNames = [
        'Kings' => 'Kings description',
        'Doubles' => 'Doubles description',
        'Suites' => 'Suites description',
        'Run of House' => 'Run of House description',
    ];

    public function _before(AcceptanceTester $I)
    {
        $I->runShellCommand('cp database/database.sqlite database/backup.sqlite');
        # cp database/database.sqlite database/backup.sqlite;

        $this->faker = Faker::create();
        $this->acceptanceTester = $I;
    }

    public function _after(AcceptanceTester $I)
    {
        $I->runShellCommand('cp database/backup.sqlite database/database.sqlite');
        $I->runShellCommand('rm database/backup.sqlite');
        # cp database/backup.sqlite database/database.sqlite;rm database/backup.sqlite;
    }

    public function _failed(AcceptanceTester $I)
    {
        $I->runShellCommand('cp database/backup.sqlite database/database.sqlite');
        $I->runShellCommand('rm database/backup.sqlite');
    }

    public function _logout(AcceptanceTester $I)
    {
        $I->click(['class' => 'dropdown-profile-menu']);
        $I->click(['id' => 'sign-out']);
        $I->amOnPage('/logout');
        $I->waitForText('LOGIN', 5);
        $I->canSeeInCurrentUrl('/');
    }

    public function _login(AcceptanceTester $I, $email)
    {
        // Make sure some other test didn't keep us logged in
        $I->amOnPage('/login');
        // Seeded user in database for acceptance tests
        $I->fillField('email', $email);
        $I->fillField('password', 'secret');
        $I->click(['class' => 'btn-primary']); // Login button

        $userId = $I->grabFromDatabase('users', 'id', ['email' => $email]);
        $this->user = \App\Models\User::find($userId);

        return $userId;
    }

    /**
     * Login as a user.
     *
     * A random user is created and random role is attached.
     *
     * @param null $user
     */
    protected function actingAsUser($login = true, $user = null)
    {
        $this->user = ($user) ?: factory(\App\Models\User::class)->create(['password' => bcrypt('secret')]);

        if ($this->user->roles()->count() == 0) {
            $this->user->roles()->attach(
                factory(\App\Models\Role::class)->create()->id
            );
        }

        if ($login) {
            $this->_login($this->acceptanceTester, $this->user->email);
        }
    }

    /**
     * Login as a Licensee User.
     *
     * A new licensee is created.
     *
     * @param mixed  $licensee  Either null to create a new licensee or an existing licensee object
     * @param string $role      Role of the licensee i.e. licensee-staff or licensee-admin
     */
    protected function actingAsLicensee($licensee = null, $role = 'licensee-staff')
    {
        $this->actingAsUser(false);
        $this->licensee = ($licensee) ?: factory(\App\Models\Licensee::class)->create();
        $this->user->roles()->detach();

        // Lets find the role id for the given role
        $roleId = \App\Models\Role::where('slug', $role)->first()->id;

        $this->user->roles()->attach(
            factory(\App\Models\Role::class)->create()->id,
            [
                'rolable_type'  => \App\Models\Licensee::class,
                'rolable_id' => $this->licensee->id,
                'role_id' => $roleId, // Licensee Staff
            ]
        );
        $this->_login($this->acceptanceTester, $this->user->email);
    }

    /**
     * Login as Hotel user
     *
     * A new hotelier is created.
     *
     * @param null $hotel
     */
    protected function actingAsHotel($hotel = null)
    {
        $this->actingAsUser(false);
        $this->hotel = ($hotel) ?: factory(\App\Models\Hotel::class)->create();
        $this->user->roles()->detach();
        $this->user->roles()->attach(
            factory(\App\Models\Role::class)->create()->id,
            [
                'rolable_type'  => \App\Models\Hotel::class,
                'rolable_id' => $this->hotel->id,
                'role_id' => 2, // Hotelier
            ]
        );
        $this->acceptanceTester->haveInDatabase('hotel_user', [
            'user_id' => $this->user->id,
            'hotel_id' => $this->hotel->id,
        ]);

        $this->_login($this->acceptanceTester, $this->user->email);
    }

    protected function createProposalRequestForHotel($I, $userId, $type = 'current', $createContract = false)
    {
        // Create a hotel and associate with this user
        $this->hotel = factory(\App\Models\Hotel::class)->create();
        $this->hotel->hoteliers()->attach($userId);

        // Create a current proposal request for the hotel
        $requestHotel = factory(\App\Models\RequestHotel::class)
            ->create([
                'hotel_id' => $this->hotel->id,
            ]);

        // Attach the user to the requestHotel
        $requestHotel->users()->attach($userId, ['hash' => str_random()]);

        // Create date range for the event (either with current dates or past dates)
        if ($type == 'current') {
            $startDate = new \Carbon\Carbon($this->faker->dateTimeBetween('+7 days', '+13 days')->format('Y-m-d'));
            $endDate = $startDate->copy()->addDays(5);
            $checkInDate = $startDate->copy()->addDays($this->faker->numberBetween(0, 3));
            $checkOutDate = $endDate->copy()->addDays($this->faker->numberBetween(0, 3));

            $eventDateRange = factory(\App\Models\EventDateRange::class)->create([
                'event_id'       => $requestHotel->proposalRequest->event_id,
                'start_date'     => $startDate,
                'end_date'       => $endDate,
                'check_in_date'  => $checkInDate,
                'check_out_date' => $checkOutDate,
            ]);
        } else {
            $endDate = new \Carbon\Carbon($this->faker->dateTimeBetween('-13 days', '-7 days')->format('Y-m-d'));
            $startDate = $endDate->copy()->subDays(5);
            $checkInDate = $startDate->copy()->subDays($this->faker->numberBetween(0, 3));
            $checkOutDate = $endDate->copy()->addDays($this->faker->numberBetween(0, 3));

            $eventDateRange = factory(\App\Models\EventDateRange::class)->create([
                'event_id'       => $requestHotel->proposalRequest->event_id,
                'start_date'     => $startDate->format('Y-m-d'),
                'end_date'       => $endDate->format('Y-m-d'),
                'check_in_date'  => $checkInDate->format('Y-m-d'),
                'check_out_date' => $checkOutDate->format('Y-m-d'),
            ]);
        }

        // Create some room request dates
        $period = new \DatePeriod(
            new \DateTime($startDate->format('Y-m-d')),
            new \DateInterval('P1D'),
            new \DateTime($endDate->format('Y-m-d') . ' 23:59:59')
        );
        $roomDates = [];
        foreach ($period as $date) {
            $roomDates[] = $date->format('Y-m-d');
        }
        $range = [
            'min' => $this->faker->numberBetween(0, 100),
            'max' => $this->faker->numberBetween(100, 200),
        ];
        $roomTypes = [
            'Kings' => $range,
            'Doubles' => $range,
            'Suites' => $range,
            'Run of House' => $range,
        ];
        foreach ($roomDates as $date) {
            foreach ($roomTypes as $type => $range) {
                $roomRequestDate = factory(\App\Models\RoomRequestDate::class)->create([
                    'event_date_range_id' => $eventDateRange->id,
                    'room_type_name' => $type,
                    'room_date' => $date,
                    'preferred_rate_min' => $range['min'],
                    'preferred_rate_max' => $range['max'],
                ]);
            }
        }

        // Create some space requests
        factory(\App\Models\SpaceRequest::class)->create([
            'event_date_range_id' => $eventDateRange->id,
            'date_requested' => $roomDates[0],
            'type' => 'Meeting',
        ]);
        factory(\App\Models\SpaceRequest::class)->create([
            'event_date_range_id' => $eventDateRange->id,
            'date_requested' => $roomDates[0],
            'type' => 'Food & Beverage',
        ]);

        $requestHotel->proposalRequest->is_meeting_space_required = 1;
        $requestHotel->proposalRequest->is_food_and_beverage_required = 1;
        $requestHotel->proposalRequest->save();

        // Create a question group and question
        $questionGroup = factory(\App\Models\RequestQuestionGroup::class)->create([
            'proposal_request_id' => $requestHotel->proposalRequest->id,
        ]);
        factory(\App\Models\RequestQuestion::class)->create([
            'request_question_group_id' => $questionGroup->id,
        ]);

        // If we have to create a contract
        if ($createContract) {
            $proposal = factory(\App\Models\Proposal::class)->create([
                'proposal_request_id' => $requestHotel->proposalRequest->id,
                'hotel_id' => $this->hotel->id,
            ]);

            factory(\App\Models\ProposalDateRange::class)->create([
                'proposal_id'   => $proposal->id,
                'event_date_range_id'   => $eventDateRange->id,
            ]);

            factory(\App\Models\Contract::class)->create([
                'proposal_id' => $proposal->id,
                'event_date_range_id' => $eventDateRange->id,
            ]);
        }

        return $requestHotel->proposalRequest;
    }

    protected function createEventAndProposalRequest(
        AcceptanceTester $I,
        $is_meeting_space_required = 0,
        $is_food_and_beverage_required = 0,
        $licensee_id = 1
    ) {
        $client_data = [
            'place_id' => 'ChIJvXBI_wO1RIYRb9NxHv19j6c',
            'name'     => 'Hotels for Hope',
            'address1' => '336 South Congress Avenue',
            'address2' => null,
            'city'     => 'Austin',
            'state'    => 'TX',
            'zip'      => '78704',
            'country'  => 'US'
        ];

        $client_id = $I->grabFromDatabase('clients', 'id', $client_data);

        $event_group_id = $this->createModel(\App\Models\EventGroup::class)[0]->id;

        $event = $this->createModel(\App\Models\Event::class, [
            'licensee_id' => $licensee_id,
            'client_id' => $client_id,
            'event_type_id' => 17,
            'event_sub_type_id' => 18,
            'event_group_id' => $event_group_id,
            'name' => $this->faker->sentence(4),
        ])[0];

        $proposal_request = $this->createModel(\App\Models\ProposalRequest::class, [
            'event_id' => $event->id,
            'client_id' => $client_id,
            'is_meeting_space_required' => $is_meeting_space_required,
            'is_food_and_beverage_required' => $is_food_and_beverage_required,
        ])[0];

        return [
            'client_data' => $client_data,
            'client_id' => $client_id,
            'client' => array_merge($client_data, ['id' => $client_id]),
            'event' => $event,
            'event_id' => $event->id,
            'proposal_request' => $proposal_request,
            'proposal_request_id' => $proposal_request->id,
        ];
    }

    protected function createDateRangeForProposalRequest(AcceptanceTester $I, $event_id)
    {
        $date_range_data = [
            'event_id' => $event_id,
            'start_date' => date('Y-m-d', strtotime('+30 days')),
            'check_in_date' => date('Y-m-d', strtotime('+33 days')),
            'check_out_date' => date('Y-m-d', strtotime('+36 days')),
            'end_date' => date('Y-m-d', strtotime('+39 days')),
            'is_chosen' => 0
        ];

        $I->haveInDatabase('event_date_ranges', $date_range_data);
        $I->wait(1);
        $event_date_range_id = $I->grabFromDatabase('event_date_ranges', 'id', $date_range_data);
        $date_range_data['id'] = $event_date_range_id;

        return $date_range_data;
    }

    /**
     * Create a location record for a proposal request
     * @param  AcceptanceTester $I
     * @param  array            $location Location database entry
     * @return array            $location Location entry including location id
     */
    protected function createLocationForProposalRequest(AcceptanceTester $I, $location = null)
    {
        if (! $location) {
            $location = $this->createModel(\App\Models\EventLocation::class)[0]->toArray();
        }

        if (! isset($location['id']) || empty($location['id'])) {
            $I->haveInDatabase('event_locations', $location);
            $location_id = $I->grabFromDatabase('event_locations', 'id', $location);
            $location['id'] = $location_id;
        }

        return $location;
    }

    protected function attachLocationToProposalRequest(AcceptanceTester $I, $proposal_request_id, $event_location_id)
    {
        $event_location_proposal_request = [
            'proposal_request_id' => $proposal_request_id,
            'event_location_id' => $event_location_id
        ];
        $I->haveInDatabase('event_location_proposal_request', $event_location_proposal_request);
        $I->wait(1);
        $id = $I->grabFromDatabase('event_location_proposal_request', 'id', $event_location_proposal_request);
        $event_location_proposal_request['id'] = $id;

        return $event_location_proposal_request;
    }

    protected function createModel($model, array $params = [], $num = 1)
    {
        $arr = [];
        for ($i = 0; $i < $num; $i++) {
            $arr[] = factory($model)->create($params);
        }
        return $arr;
    }

    protected function createAndSubmitValidProposal(AcceptanceTester $I)
    {
        $I->waitForElement('#proposal-detail-tabs', 5);
        $I->waitForText('Sleeping Rooms', 5);
        $I->waitForText('(Rooms)', 5);
        $I->click('#description-0');
        $I->waitForText('Room description', 2);
        $I->fillField('#room-description-accommodation', $this->faker->sentence);
        $I->click('#save-description');
        $I->click('#description-1');
        $I->waitForText('Room description', 2);
        $I->fillField('#room-description-accommodation', $this->faker->sentence);
        $I->click('#save-description');
        $I->click('#summary-tab');

        // Assert cannot submit an invalid proposal yet
        $I->seeElement('#submit-proposal', ['disabled' => true]);

        // Fill out the details we need to make a valid proposal submission
        $I->click('#accommodation-tab');
        $I->waitForText('(Rooms)', 5);
        $I->waitForElement('#deposit-policy', 5);
        $depositPolicy = $this->faker->sentence;
        $I->fillField('#deposit-policy', $depositPolicy);
        $I->click('#question-tab');
        $I->waitForElement('textarea.question', 5);
        $answer = $this->faker->sentence;
        $I->fillField('textarea.question', $answer);
        $I->wait(2); // FIXME: This should be removable. not sure why this takes a few seconds to update.
        $I->dontSee('(required)');

        // Return back to the summary page
        $I->click('#summary-tab');
        $I->waitForElement('#submit-proposal', 5);

        // Assert the data we saved shows on the summary page
        $I->see($depositPolicy);
        $I->see($answer);

        // The proposal is still invalid as there are few empty room descriptions
        $I->seeElement('#submit-proposal', ['disabled' => true]);

        // Need to add the empty room descriptions
        $I->click('#accommodation-tab');
        $I->waitForText('(Rooms)', 5);
        $I->click('#description-2');
        $I->waitForText('Room description', 2);
        $I->fillField('#room-description-accommodation', $this->faker->sentence);
        $I->click('#save-description');
        $I->click('#description-3');
        $I->waitForText('Room description', 2);
        $I->fillField('#room-description-accommodation', $this->faker->sentence);
        $I->click('#save-description');
        $I->click('#summary-tab');

        //The proposal is still invalid as we need to add honor bid until
        $I->seeElement('#submit-proposal', ['disabled' => true]);
        $I->click('#datepicker');
        //FIXME: Maybe there is a better way to choose date
        $I->click('div.datepicker-days > table > thead > tr:nth-child(2) > th:nth-child(3)');
        $I->click('div.datepicker-days > table > tbody > tr:nth-child(5) > td:nth-child(3)');


        // Assert we can submit the proposal
        $I->seeElement('#submit-proposal', ['disabled' => false]);

        // Submit the data
        $I->click('#submit-proposal');
    }

    protected function createProposalForExistingUser(AcceptanceTester $I)
    {
        $this->userId = $I->grabFromDatabase('users', 'id', ['email' => 'harry@hotelier.com']);

        $this->proposalRequest = $this->createProposalRequestForHotel($I, $this->userId);

        $this->requestHotel = $this->proposalRequest->requestHotels()->where('hotel_id', $this->hotel->id)->first();
        $this->hash = str_random();
        $this->requestHotel->users()
            ->attach(
                $this->userId,
                [
                    'hash'  => $this->hash,
                ]
            );

        $I->wantTo('Submit a valid set of proposal details when not logged in.');

        $I->amOnPage("/hotels/proposal-requests/".$this->proposalRequest->id."/gateway?u=".$this->userId."&h=".$this->hash."&action=detail");
        $I->waitForElement('#save-proposal', 5);
        $I->click('#save-proposal');

        $this->createAndSubmitValidProposal($I);

        //Confirm that user who get the invitation is user who submits proposal
        $I->waitForElement('#confirmUser', 2);
        $I->see('No');
        $I->see('Yes');
    }

    protected function formatDateRange($startDate, $endDate)
    {
        if ($startDate->year !== $endDate->year) {
            return $startDate->format('M j, Y'). ' to '. $endDate->format('M j, Y');
        }

        if ($startDate->month !== $endDate->month) {
            return $startDate->format('M j'). ' to '. $endDate->format('M j'). ', '. $startDate->format('Y');
        }

        return $startDate->format('M j'). '-'. $endDate->format('j'). ', '. $startDate->format('Y');
    }

    protected function createContract(AcceptanceTester $I)
    {
        $I->amGoingTo("create a contract");

        $eventStartDate = Carbon::now()->addDays(10);

        $contract = factory(\App\Models\Contract::class)->create([
            'start_date' => $eventStartDate->format('Y-m-d'),
            'check_in_date' => $eventStartDate->addDays(2)->format('Y-m-d'),
            'check_out_date' => $eventStartDate->addDays(2)->format('Y-m-d'),
            'end_date' => $eventStartDate->addDays(2)->format('Y-m-d'),
            'cutoff_date' => $eventStartDate->subDays(15),
            'questions' => json_encode([
                [
                    'index'    => 0,
                    'id'       => $this->faker->numberBetween(1, 10),
                    'question' => $this->faker->sentence,
                    'answer'   => $this->faker->sentence,
                    'group' => $this->faker->word,
                ]
            ]),
            'meeting_spaces' => json_encode([
                [
                    'index'         => 0,
                    'id'            => $this->faker->numberBetween(1, 10),
                    'date'          => $eventStartDate->addDays(13)->format('Y-m-d'),
                    'amount'        => $this->faker->randomFloat(2, 200, 1000),
                    'amount_units'  => $this->faker->randomElement(['total', 'per person']),
                    'description'   => $this->faker->sentence,
                    'complimentary' => $this->faker->boolean,
                    'conditions'    => $this->faker->sentence,
                    'start_time'    => '08:00:00',
                    'end_time'      => '09:00:00',
                    'name'          => $this->faker->word,
                    'attendees'     => $this->faker->randomNumber,
                    'budget'        => $this->faker->randomFloat(2, 200, 1000),
                    'budget_units'  => 'total',
                    'room_type'     => 'Meeting',
                    'layout'        => '',
                    'requests'      => $this->faker->paragraph,
                    'equipment'     => json_encode([
                        'name'     => $this->faker->word,
                        'quantity' => $this->faker->randomNumber(1),
                    ]),
                    'meal'          =>  null,
                    'notes'        => null,
                ]
            ]),
            'food_and_beverage' => json_encode([
                [
                    'index'         => 0,
                    'id'            => $this->faker->numberBetween(1, 10),
                    'date'          => $eventStartDate->addDays(12)->format('Y-m-d'),
                    'amount'        => $this->faker->randomFloat(2, 200, 1000),
                    'amount_units'  => $this->faker->randomElement(['total', 'per person']),
                    'description'   => $this->faker->sentence,
                    'complimentary' => $this->faker->boolean,
                    'conditions'    => $this->faker->sentence,
                    'start_time'    => '08:00:00',
                    'end_time'      => '09:00:00',
                    'name'          => $this->faker->word,
                    'attendees'     => $this->faker->randomNumber,
                    'budget'        => $this->faker->randomFloat(2, 200, 1000),
                    'budget_units'  => 'total',
                    'room_type'     => 'Diner',
                    'layout'        => '',
                    'requests'      => $this->faker->paragraph,
                    'equipment'     => json_encode([]),
                    'meal'          => 'Breakfast',
                    'notes'         => $this->faker->sentence,
                ]
            ]),
        ]);

        // Update the proposal request to set the is_meeting_space_required and f&b required to true
        $contract->proposal->proposalRequest->is_meeting_space_required = true;
        $contract->proposal->proposalRequest->is_food_and_beverage_required = true;
        $contract->proposal->proposalRequest->save();

        $I->amGoingTo("create a room sets for the contract");
        // Create the room sets for all dates
        for ($date = $contract['start_date']; $date->lte($contract['end_date']); $date->addDays(1)) {
            // Add room set for each room type/name for this date
            foreach ($this->roomNames as $roomName => $roomDescription) {
                factory(\App\Models\RoomSet::class)->create([
                    'contract_id' => $contract->id,
                    'name' => $roomName,
                    'description'   => $roomDescription,
                    'reservation_date' => $date,
                ]);
            }
        }

        // Lets add some terms to the contract
        for ($i = 1; $i <= 5; $i++) {
            factory(\App\Models\ContractTerm::class)->create([
                'contract_term_group_id' => factory(\App\Models\ContractTermGroup::class)->create([
                    'contract_id' => $contract->id,
                ])->id
            ]);
        }

        return $contract;
    }
}
