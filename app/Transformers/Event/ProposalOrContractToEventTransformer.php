<?php

namespace App\Transformers\Event;

use App\Models\Contract;
use App\Models\Proposal;
use App\Transformers\Transformer;

class ProposalOrContractToEventTransformer extends Transformer
{
    /**
     * Transform a single object
     *
     * @param $object
     *
     * @return mixed
     */
    public function transform($object)
    {
        switch (get_class($object)) {
            case Proposal::class:
                return $this->transformProposal($object);
                break;
            case Contract::class:
                return $this->transformContract($object);
                break;
        }
    }

    /**
     * Transform Proposal object into view-readable "Event"
     *
     * @param Proposal $proposal
     *
     * @return object
     */
    private function transformProposal($proposal)
    {
        return (object)[
            'event_id'              => $proposal->proposalRequest->event->id,
            'proposal_id'           => $proposal->id,
            'contract_id'           => null,
            'stage'                 => 'RFP',
            'name'                  => $proposal->proposalRequest->event->name,
            'cutoff_date'           => $proposal->proposalRequest->cutoff_date,
            'check_in_date'         => $proposal->dateRanges->first()->check_in_date,
            'check_out_date'         => $proposal->dateRanges->first()->check_out_date,
        ];
    }

    private function transformContract($contract)
    {
        //  @TODO - Fill this in once booking portal infrastructure is implemented
        $inBookingStage = false;

        return (object)[
            'event_id'              => $contract->proposalRequest->event->id,
            'proposal_id'           => null,
            'contract_id'           => $contract->id,
            'stage'                 => $inBookingStage ? 'Book' : 'Contract',
            'name'                  => $contract->proposalRequest->event->name,
            'cutoff_date'           => $contract->proposalRequest->cutoff_date,
            'check_in_date'         => $contract->dateRange->check_in_date,
            'check_out_date'         => $contract->dateRange->check_out_date,
        ];
    }
}
