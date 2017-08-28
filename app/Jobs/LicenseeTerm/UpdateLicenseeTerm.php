<?php

namespace App\Jobs\LicenseeTerm;

use App\Events\Licensee\Terms\LicenseeTermWasUpdated;
use App\Repositories\Contracts\LicenseeTermRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateLicenseeTerm implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int
     */
    private $termId;

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
     * @param int $termId
     * @param string $title
     * @param string $description
     */
    public function __construct($termId, $title, $description)
    {
        $this->termId = $termId;
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
        $termRepo->update(
            $this->termId,
            [
                'description'   => $this->description,
                'title'   => $this->title,
            ]
        );

        event(
            new LicenseeTermWasUpdated(
                $this->termId,
                $this->description
            )
        );
    }
}
