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

class ProposalRequestForHotelTransformer extends Transformer
{
    use AssemblesData;

    /**
     * @var ProposalRequestTransformer
     */
    private $parentTransformer;

    /**
     * ProposalRequestTransformer constructor.
     * @param ProposalRequestTransformer $parentTransformer
     */
    public function __construct(ProposalRequestTransformer $parentTransformer)
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

        return (object)collect($base)
            ->except([
                //'client',
                //'client_id',
                //'client_name',
                //'client_place_id',
                'planner',
                'planner_id',
                'planner_name',
                'planner_place_id',
                'is_visible_to_client',
                'is_visible_to_planner',
                'hotels',
            ])
            ->toArray();

        return $base;
    }
}
