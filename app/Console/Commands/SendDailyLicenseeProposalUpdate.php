<?php

namespace App\Console\Commands;

use App\Events\Licensee\DailySummaryIsNeeded;
use App\Repositories\Contracts\LicenseeRepositoryInterface;
use Illuminate\Console\Command;

class SendDailyLicenseeProposalUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:licensee-daily-proposal-summary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily summary of proposal submissions to all licensees.';

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
        $licensees = $this
            ->licenseeRepo
            ->all()
            ->where('receive_daily_recap', true);

        foreach ($licensees as $licensee) {
            event(
                new DailySummaryIsNeeded(
                    $licensee
                )
            );
        }

        $this->info(
            sprintf(
                '%d daily digest emails sent to licensees.',
                count($licensees)
            )
        );
    }
}
