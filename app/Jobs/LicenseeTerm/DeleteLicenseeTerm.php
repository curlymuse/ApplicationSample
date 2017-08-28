<?php

namespace App\Jobs\LicenseeTerm;

use App\Events\Licensee\Terms\LicenseeTermWasDeleted;
use App\Repositories\Contracts\LicenseeTermRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class DeleteLicenseeTerm implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int
     */
    private $termId;

    /**
     * Create a new job instance.
     *
     * @param int $termId
     */
    public function __construct($termId)
    {
        $this->termId = $termId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(LicenseeTermRepositoryInterface $termRepo)
    {
        $termRepo->delete($this->termId);

        event(
            new LicenseeTermWasDeleted(
                $this->termId
            )
        );
    }
}
