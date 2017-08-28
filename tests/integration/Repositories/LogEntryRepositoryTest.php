<?php

namespace Tests\Integration\Repositories;

use App\Models\Contract;
use App\Models\Event;
use App\Models\Hotel;
use App\Models\Licensee;
use App\Models\LogEntry;
use App\Models\Proposal;
use App\Models\ProposalRequest;
use App\Models\User;
use App\Repositories\Contracts\LogEntryRepositoryInterface;
use Tests\TestCase;

/**
 * Class LogEntryRepositoryTest
 *
 * @coversBaseClass \App\Repositories\LogEntryRepository
 */
class LogEntryRepositoryTest extends TestCase
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @var \App\Repositories\Contracts\LogEntryRepositoryInterface
     */
    private $repo;

    /**
     * @before
     */
    public function setUp()
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();

        $this->repo = app(LogEntryRepositoryInterface::class);
    }

    /**
     * @covers ::getCatalogForAccountAndDates
     */
    public function test_getCatalogForAccountAndDates()
    {
        $accountClass = $this->faker->randomElement([Licensee::class, Hotel::class]);
        $accountObject = factory($accountClass)->create();
        $user = factory(User::class)->create();

        $keyIndex = [];
        for ($i = 0; $i < $this->faker->numberBetween(2, 3); $i++) {
            $action = $this->faker->word;
            $keyIndex[$action] = 0;
            for ($j = 0; $j < $this->faker->numberBetween(1, 2); $j++) {
                LogEntry::create([
                    'account_type'  => $accountClass,
                    'account_id'    => $accountObject->id,
                    'action'    => $action,
                    'user_id'   => $user->id,
                ]);
                $keyIndex[$action]++;
            }
        }

        $results = $this->repo->getCatalogForAccountAndDates($accountClass, $accountObject->id);

        foreach ($keyIndex as $action => $count) {
            $this->assertArrayHasKey($action, $results);
            $this->assertCount($count, $results[$action]);
        }
    }
    
    /**
     * @covers ::emailLoggedForUserAndSubject
     */
    public function test_emailLoggedForUserAndSubject()
    {
        $user = factory(User::class)->create();
        $event = factory(Event::class)->create();
        $class = $this->faker->word;

        $this->assertFalse(
            $this->repo->emailLoggedForUserAndSubject($user->id, $class, get_class($event), $event->id)
        );

        factory(LogEntry::class)->create([
            'user_id'   => $user->id,
            'account_type'  => User::class,
            'account_id'    => $user->id,
            'action'    => 'mail:sent',
            'subject_type'  => get_class($event),
            'subject_id'    => $event->id,
            'notes'     => $class,
        ]);

        $this->assertTrue(
            $this->repo->emailLoggedForUserAndSubject($user->id, $class, get_class($event), $event->id)
        );
    }

    /**
     * @covers ::allActivitiesForProposal
     */
    public function test_allActivitiesForProposal()
    {
        $proposal = factory(Proposal::class)->create();

        $proposalEntry = factory(LogEntry::class)->create([
            'subject_type'  => Proposal::class,
            'subject_id'    => $proposal->id,
        ]);
        $proposalRequestEntry = factory(LogEntry::class)->create([
            'subject_type'  => ProposalRequest::class,
            'subject_id'    => $proposal->proposal_request_id,
        ]);
        $excludeEntry = factory(LogEntry::class)->create();
        $excludeEntryForProposalRequest = factory(LogEntry::class)->create([
            'subject_type'  => ProposalRequest::class,
            'subject_id'    => 1235,
        ]);

        $entries = $this->repo->allActivitiesForProposal($proposal->id);

        $this->assertContains($proposalEntry->id, $entries->pluck('id'));
        $this->assertContains($proposalRequestEntry->id, $entries->pluck('id'));
        $this->assertNotContains($excludeEntry->id, $entries->pluck('id'));
        $this->assertNotContains($excludeEntryForProposalRequest->id, $entries->pluck('id'));
    }

    /**
     * @covers ::allActivitiesForContract
     */
    public function test_allActivitiesForContract()
    {
        $contract = factory(Contract::class)->create();

        $includeEntry = factory(LogEntry::class)->create([
            'subject_type'  => Contract::class,
            'subject_id'    => $contract->id,
        ]);
        $excludeEntry = factory(LogEntry::class)->create();

        $entries = $this->repo->allActivitiesForContract($contract->id);

        $this->assertContains($includeEntry->id, $entries->pluck('id'));
        $this->assertNotContains($excludeEntry->id, $entries->pluck('id'));
    }
}