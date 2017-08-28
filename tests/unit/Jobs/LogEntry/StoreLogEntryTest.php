<?php

namespace Tests\Unit\Jobs\LogEntry;

use App\Events\LogEntry\EventWasLogged;
use App\Jobs\LogEntry\StoreLogEntry;
use App\Models\Contract;
use App\Models\Hotel;
use App\Models\Licensee;
use App\Models\LogEntry;
use App\Models\Proposal;
use App\Models\ProposalRequest;
use App\Models\User;
use App\Repositories\Contracts\LogEntryRepositoryInterface;
use Tests\TestCase;

/**
 * Class StoreLogEntryTest
 *
 * @coversBaseClass App\Jobs\LogEntry\StoreLogEntry
 */
class StoreLogEntryTest extends TestCase
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @var \App\Repositories\Contracts\LogEntryRepositoryInterface
     */
    private $logEntryRepo;

    /**
     * @before
     */
    public function setUp()
    {
        parent::setUp();

        $this->faker = \Faker\Factory::create();

        $this->logEntryRepo = $this->expectsRepository(LogEntryRepositoryInterface::class);
    }

    public function test_handle()
    {
        $accountType = $this->faker->randomElement([Licensee::class, Hotel::class]);
        $account = factory($accountType)->create();
        $subjectType = $this->faker->randomElement([Proposal::class, Contract::class, ProposalRequest::class]);
        $subject = factory($subjectType)->create();
        $user = factory(User::class)->create();
        $action = $this->faker->word;
        $notes = $this->faker->paragraph;
        $description = $this->faker->sentence;

        $entry = factory(LogEntry::class)->create();

        $this->logEntryRepo->shouldReceive('store')
            ->once()
            ->with([
                'account_type'  => $accountType,
                'account_id'    => $account->id,
                'user_id'       => $user->id,
                'action'        => $action,
                'subject_type'  => $subjectType,
                'subject_id'    => $subject->id,
                'description'   => $description,
                'notes'         => $notes,
            ])
            ->andReturn($entry);

        $this->expectsEvents(EventWasLogged::class);

        dispatch(
            new StoreLogEntry(
                $accountType,
                $account->id,
                $user->id,
                $action,
                $subjectType,
                $subject->id,
                $description,
                $notes
            )
        );
    }
}