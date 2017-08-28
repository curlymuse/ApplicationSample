<?php

namespace App\Console\Commands;

use App\Events\Hotel\ProposalCutoffIsApproaching;
use App\Events\Licensee\ProposalRequestCutoffIsApproaching;
use App\Repositories\Contracts\ProposalRepositoryInterface;
use App\Repositories\Contracts\RequestHotelRepositoryInterface;
use Illuminate\Console\Command;

class SendProposalExpiringReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:proposals-expiring-for-licensee';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify licensees who have taken no action on proposals that the proposals will expire soon';

    /**
     * @var ProposalRepositoryInterface
     */
    private $proposalRepo;

    /**
     * Create a new command instance.
     *
     * @param ProposalRepositoryInterface $proposalRepo
     * @internal param RequestHotelRepositoryInterface $requestHotelRepo
     */
    public function __construct(ProposalRepositoryInterface $proposalRepo)
    {
        parent::__construct();

        $this->proposalRepo = $proposalRepo;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $daysToExpire = config('resbeat.notification.contact-licensee-days-before-proposal-expiration');
        $proposals = $this->proposalRepo->allWithUpcomingExpirationAndNoActionTaken($daysToExpire);

        foreach ($proposals as $proposal) {
            event(
                new ProposalCutoffIsApproaching(
                    $proposal
                )
            );
        }

        $this->info(
            sprintf(
                '%d reminders sent out to licensee, informing them of the approaching cutoff date in %d days.',
                count($proposals),
                $daysToExpire
            )
        );
    }
}
