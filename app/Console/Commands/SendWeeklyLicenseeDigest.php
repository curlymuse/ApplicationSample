<?php

namespace App\Console\Commands;

use App\Events\Licensee\WeeklySummaryIsNeeded;
use App\Repositories\Contracts\LicenseeRepositoryInterface;
use Illuminate\Console\Command;

class SendWeeklyLicenseeDigest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:licensee-weekly-digest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send weekly digest email with all RFP activities to each licensee.';

    /**
     * @var LicenseeRepositoryInterface
     */
    private $licenseeRepo;

    /**
     * Create a new command instance.
     *
     * @param LicenseeRepositoryInterface $licenseeRepo
     */
    public function __construct(LicenseeRepositoryInterface $licenseeRepo)
    {
        parent::__construct();

        $this->licenseeRepo = $licenseeRepo;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $licensees = $this->licenseeRepo->all();

        foreach ($licensees as $licensee) {
            event(
                new WeeklySummaryIsNeeded(
                    $licensee
                )
            );
        }

        $this->info(
            sprintf(
                '%d weekly digest emails sent to licensees.',
                count($licensees)
            )
        );
    }
}
