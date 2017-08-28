<?php

namespace App\Jobs\LicenseeTermGroup;

use App\Events\Licensee\TermGroups\LicenseeTermGroupWasCreated;
use App\Repositories\Contracts\LicenseeTermGroupRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateLicenseeTermGroup implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int
     */
    private $licenseeId;

    /**
     * @var string
     */
    private $name;

    /**
     * Create a new job instance.
     *
     * @param int $licenseeId
     * @param string $name
     */
    public function __construct($licenseeId, $name)
    {
        $this->licenseeId = $licenseeId;
        $this->name = $name;
    }

    /**
     * Execute the job.
     *
     * @param LicenseeTermGroupRepositoryInterface $groupRepo
     */
    public function handle(LicenseeTermGroupRepositoryInterface $groupRepo)
    {
        $group = $groupRepo->storeForLicensee(
            $this->licenseeId,
            $this->name
        );

        event(
            new LicenseeTermGroupWasCreated(
                $this->licenseeId,
                $this->name
            )
        );

        return $group->id;
    }
}
