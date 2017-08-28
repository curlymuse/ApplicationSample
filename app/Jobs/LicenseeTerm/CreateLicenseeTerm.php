<?php

namespace App\Jobs\LicenseeTerm;

use App\Events\Licensee\Terms\LicenseeTermWasCreated;
use App\Repositories\Contracts\LicenseeTermRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateLicenseeTerm implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int
     */
    private $groupId;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $title;

    /**
     * Create a new job instance.
     *
     * @param int $groupId
     * @param string $title
     * @param string $description
     */
    public function __construct($groupId, $title, $description)
    {
        $this->groupId = $groupId;
        $this->description = $description;
        $this->title = $title;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(LicenseeTermRepositoryInterface $termRepo)
    {
        $term = $termRepo->storeForTermGroup(
            $this->groupId,
            $this->title,
            $this->description
        );

        event(
            new LicenseeTermWasCreated(
                $this->groupId,
                $this->title,
                $this->description
            )
        );

        return $term->id;
    }
}
