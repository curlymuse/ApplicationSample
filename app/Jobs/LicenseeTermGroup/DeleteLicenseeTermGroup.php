<?php

namespace App\Jobs\LicenseeTermGroup;

use App\Events\Licensee\TermGroups\LicenseeTermGroupWasDeleted;
use App\Repositories\Contracts\LicenseeTermGroupRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class DeleteLicenseeTermGroup implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var
     */
    private $groupId;

    /**
     * Create a new job instance.
     *
     * @param $groupId
     */
    public function __construct($groupId)
    {
        $this->groupId = $groupId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(LicenseeTermGroupRepositoryInterface $groupRepo)
    {
        $groupRepo->delete($this->groupId);

        event(
            new LicenseeTermGroupWasDeleted(
                $this->groupId
            )
        );
    }
}
