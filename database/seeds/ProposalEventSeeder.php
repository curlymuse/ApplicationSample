<?php

use App\Models\Contract;
use App\Models\Proposal;
use App\Models\ProposalRequest;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ProposalEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $hotelId = 1;

        $this->seedRFP($hotelId, 'RFP');
        $this->seedRFP($hotelId, 'Contract');
    }

    /**
     * See a request, complete with an event and proposal, and any other
     * objects needed to complete the passed-in stage.
     *
     * @param int $hotelId
     * @param string $stage
     */
    private function seedRFP($hotelId, $stage)
    {
        $event = factory(\App\Models\Event::class)->create();
        $proposalRequest = factory(ProposalRequest::class)->create(['event_id' => $event->id]);
        $dateRange = factory(\App\Models\EventDateRange::class)->create([
            'event_id'  => $event->id,
        ]);
        $proposal = factory(Proposal::class)->create([
            'hotel_id'  => $hotelId,
            'proposal_request_id' => $proposalRequest->id,
        ]);
        $proposal->dateRanges()->attach($dateRange);

        if ($stage != 'RFP') {
            $contract = factory(Contract::class)->create([
                'hotel_id'  => $hotelId,
                'event_date_range_id' => $dateRange->id,
                'proposal_request_id' => $proposalRequest->id,
            ]);
        }
    }
}
