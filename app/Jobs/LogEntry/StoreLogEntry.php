<?php

namespace App\Jobs\LogEntry;

use App\Events\LogEntry\EventWasLogged;
use App\Repositories\Contracts\LogEntryRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class StoreLogEntry implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    private $accountType;

    /**
     * @var int
     */
    private $accountId;

    /**
     * @var int
     */
    private $userId;

    /**
     * @var string
     */
    private $action;

    /**
     * @var string
     */
    private $subjectType;

    /**
     * @var int
     */
    private $subjectId;

    /**
     * @var string
     */
    private $notes;

    /**
     * @var string
     */
    private $description;

    /**
     * Create a new job instance.
     *
     * @param string $accountType
     * @param int $accountId
     * @param int $userId
     * @param string $action
     * @param string $subjectType
     * @param int $subjectId
     * @param string $description
     * @param string $notes
     */
    public function __construct(
        $accountType,
        $accountId,
        $userId,
        $action,
        $subjectType,
        $subjectId,
        $description,
        $notes
    )
    {
        $this->accountType = $accountType;
        $this->accountId = $accountId;
        $this->userId = $userId;
        $this->action = $action;
        $this->subjectType = $subjectType;
        $this->subjectId = $subjectId;
        $this->notes = $notes;
        $this->description = $description;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(LogEntryRepositoryInterface $logEntryRepo)
    {
        $entry = $logEntryRepo->store([
            'account_type'  => $this->accountType,
            'account_id'    => $this->accountId,
            'user_id'       => $this->userId,
            'action'        => $this->action,
            'subject_type'  => $this->subjectType,
            'subject_id'    => $this->subjectId,
            'description'   => $this->description,
            'notes'         => $this->notes,
        ]);

        event(
            new EventWasLogged(
                $entry
            )
        );
    }
}
