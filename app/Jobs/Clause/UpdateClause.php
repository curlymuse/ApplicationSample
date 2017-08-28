<?php

namespace App\Jobs\Clause;

use App\Events\Licensee\Clause\ClauseWasUpdated;
use App\Repositories\Contracts\ClauseRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateClause implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int
     */
    private $clauseId;

    /**
     * @var array
     */
    private $attributes;

    /**
     * Create a new job instance.
     *
     * @param int $clauseId
     * @param array $attributes
     */
    public function __construct($clauseId, $attributes = [])
    {
        $this->clauseId = $clauseId;
        $this->attributes = $attributes;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ClauseRepositoryInterface $clauseRepo)
    {
        $clauseRepo->update(
            $this->clauseId,
            $this->attributes
        );

        event(
            new ClauseWasUpdated(
                $this->clauseId,
                $this->attributes
            )
        );
    }
}
