<?php

namespace App\Transformers\ProposalRequest;

use App\Assemblers\Hotel\HotelsAndRecipientsForProposalRequestAssembler;
use App\Traits\AssemblesData;
use App\Transformers\Client\ClientTransformer;
use App\Transformers\EventDateRange\EventDateRangeTransformer;
use App\Transformers\EventLocation\EventLocationTransformer;
use App\Transformers\Hotel\HotelTransformer;
use App\Transformers\Hotel\HotelWithRecipientsTransformer;
use App\Transformers\Planner\PlannerTransformer;
use App\Transformers\RequestNote\RequestNoteTransformer;
use App\Transformers\RequestQuestionGroup\RequestQuestionGroupTransformer;
use App\Transformers\Transformer;
use Carbon\Carbon;

class UnreadProposalRequestWithHashTransformer extends Transformer
{
    /**
     * @var ProposalRequestTransformer
     */
    private $parentTransformer;

    /**
     * ProposalRequestTransformer constructor.
     * @param ProposalRequestTransformer $parentTransformer
     */
    public function __construct(ProposalRequestForHotelTransformer $parentTransformer)
    {
        $this->parentTransformer = $parentTransformer;
    }

    /**
     * Transform a single object
     *
     * @param $proposalRequest
     *
     * @return mixed
     */
    public function transform($proposalRequest)
    {
        $base = $this->parentTransformer->transform($proposalRequest);

        $link = sprintf(
            route('hotels.proposal-requests.gateway', [
                'requestId' => $proposalRequest->id
            ]) . '?u=%d&h=%s&action=detail',
            $proposalRequest->user_id,
            $proposalRequest->hash
        );

        return (object)collect($base)
            ->merge([
                'link'  => $link,
                'hash'  => $proposalRequest->hash,
                'hotel_id'  => $proposalRequest->hotel_id,
            ])
            ->toArray();

        return $base;
    }
}
