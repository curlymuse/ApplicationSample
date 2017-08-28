<?php

namespace App\Console\Commands;

use App\Events\Licensee\ProposalRequestCutoffIsApproaching;
use App\Repositories\Contracts\RequestHotelRepositoryInterface;
use Illuminate\Console\Command;

class SendRequestExpiringReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:proposal-requests-expiring-for-hotels';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify hotels who have taken no action that an RFP is about to expire.';

    /**
     * @var RequestHotelRepositoryInterface
     */
    private $requestHotelRepo;

    /**
     * Create a new command instance.
     *
     * @param RequestHotelRepositoryInterface $requestHotelRepo
     */
    public function __construct(RequestHotelRepositoryInterface $requestHotelRepo)
    {
        parent::__construct();

        $this->requestHotelRepo = $requestHotelRepo;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $daysToExpire = config('resbeat.notification.contact-hotel-days-before-request-cutoff');
        $requestHotels = $this->requestHotelRepo->allWithExpiringRequestAndNoActionTaken($daysToExpire);

        foreach ($requestHotels as $requestHotel) {
            event(
                new ProposalRequestCutoffIsApproaching(
                    $requestHotel->proposalRequest,
                    $requestHotel->hotel
                )
            );
        }

        $this->info(
            sprintf(
                '%d reminders sent out to hotels, informing them of the approaching cutoff date in %d days.',
                count($requestHotels),
                $daysToExpire
            )
        );
    }
}
