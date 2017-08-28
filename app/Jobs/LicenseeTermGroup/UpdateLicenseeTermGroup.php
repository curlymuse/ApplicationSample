<?php

namespace App\Jobs\LicenseeTermGroup;

use App\Events\Licensee\TermGroups\LicenseeTermGroupWasUpdated;
use App\Repositories\Contracts\LicenseeTermGroupRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateLicenseeTermGroup implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int
     */
    private $groupId;

    /**
     * @var string
     */
    private $name;

    /**
     * Create a new job instance.
     *
     * @param int $groupId
     * @param string $name
     */
    public function __construct($groupId, $name)
    {
        $this->groupId = $groupId;
        $this->name = $name;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(LicenseeTermGroupRepositoryInterface $groupRepo)
    {
        $groupRepo->update(
            $this->groupId,
            [
                'name'  => $this->name,
            ]
        );

        event(
            new LicenseeTermGroupWasUpdated(
                $this->groupId,
                $this->name
            )
        );
    }
}
