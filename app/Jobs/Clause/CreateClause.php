<?php

namespace App\Jobs\Clause;

use App\Events\Licensee\Clause\ClauseWasCreated;
use App\Repositories\Contracts\ClauseRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateClause implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int
     */
    private $licenseeId;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $body;

    /**
     * @var bool
     */
    private $isDefault;

    /**
     * Create a new job instance.
     *
     * @param int $licenseeId
     * @param string $title
     * @param string $body
     * @param bool $isDefault
     */
    public function __construct($licenseeId, $title, $body, $isDefault)
    {
        $this->licenseeId = $licenseeId;
        $this->title = $title;
        $this->body = $body;
        $this->isDefault = $isDefault;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ClauseRepositoryInterface $clauseRepo)
    {
        $attributes = [
            'title'     => $this->title,
            'body'      => $this->body,
            'is_default'    => $this->isDefault,
        ];

        $clause = $clauseRepo->storeForLicensee(
            $this->licenseeId,
            $attributes
        );

        event(
            new ClauseWasCreated(
                $this->licenseeId,
                $attributes
            )
        );

        return $clause->id;
    }
}
