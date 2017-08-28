<?php

namespace App\Jobs\Clause;

use App\Events\Licensee\Clause\ClauseWasDeleted;
use App\Repositories\Contracts\ClauseRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class DeleteClause implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int
     */
    private $clauseId;

    /**
     * Create a new job instance.
     *
     * @param int $clauseId
     */
    public function __construct($clauseId)
    {
        $this->clauseId = $clauseId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ClauseRepositoryInterface $clauseRepo)
    {
        $clauseRepo->delete($this->clauseId);

        event(
            new ClauseWasDeleted(
                $this->clauseId
            )
        );
    }
}
