<?php

namespace App\Transformers\ProposalRequest;

use App\Assemblers\Hotel\HotelsAndRecipientsForProposalRequestAssembler;
use App\Models\Tag;
use App\Traits\AssemblesData;
use App\Transformers\Attachment\AttachmentTransformer;
use App\Transformers\Client\ClientTransformer;
use App\Transformers\EventDateRange\EventDateRangeTransformer;
use App\Transformers\EventLocation\EventLocationTransformer;
use App\Transformers\Hotel\HotelTransformer;
use App\Transformers\Hotel\HotelWithRecipientsTransformer;
use App\Transformers\PaymentMethod\PaymentMethodTransformer;
use App\Transformers\Planner\PlannerTransformer;
use App\Transformers\RequestNote\RequestNoteTransformer;
use App\Transformers\RequestQuestionGroup\RequestQuestionGroupTransformer;
use App\Transformers\ReservationMethod\ReservationMethodTransformer;
use App\Transformers\Tag\TagTransformer;
use App\Transformers\Transformer;
use Carbon\Carbon;

class ProposalRequestTransformer extends Transformer
{
    use AssemblesData;

    /**
     * @var EventDateRangeTransformer
     */
    private $dateRangeTransformer;

    /**
     * @var RequestNoteTransformer
     */
    private $noteTransformer;

    /**
     * @var RequestQuestionGroupTransformer
     */
    private $questionGroupTransformer;

    /**
     * @var HotelTransformer
     */
    private $hotelTransformer;

    /**
     * @var EventLocationTransformer
     */
    private $locationTransformer;

    /**
     * @var ClientTransformer
     */
    private $clientTransformer;

    /**
     * @var PlannerTransformer
     */
    private $plannerTransformer;

    /**
     * @var AttachmentTransformer
     */
    private $attachmentTransformer;

    /**
     * @var TagTransformer
     */
    private $tagTransformer;

    /**
     * @var ReservationMethodTransformer
     */
    private $reservationMethodTransformer;

    /**
     * @var PaymentMethodTransformer
     */
    private $paymentMethodTransformer;

    /**
     * ProposalRequestTransformer constructor.
     * @param EventDateRangeTransformer $dateRangeTransformer
     * @param RequestNoteTransformer $noteTransformer
     * @param RequestQuestionGroupTransformer $questionGroupTransformer
     * @param HotelWithRecipientsTransformer $hotelTransformer
     * @param EventLocationTransformer $locationTransformer
     * @param ClientTransformer $clientTransformer
     * @param PlannerTransformer $plannerTransformer
     * @param AttachmentTransformer $attachmentTransformer
     * @param TagTransformer $tagTransformer
     * @param ReservationMethodTransformer $reservationMethodTransformer
     * @param PaymentMethodTransformer $paymentMethodTransformer
     * @internal param HotelsAndRecipientsForProposalRequestAssembler $hotelAssembler
     */
    public function __construct(
        EventDateRangeTransformer $dateRangeTransformer,
        RequestNoteTransformer $noteTransformer,
        RequestQuestionGroupTransformer $questionGroupTransformer,
        HotelWithRecipientsTransformer $hotelTransformer,
        EventLocationTransformer $locationTransformer,
        ClientTransformer $clientTransformer,
        PlannerTransformer $plannerTransformer,
        AttachmentTransformer $attachmentTransformer,
        TagTransformer $tagTransformer,
        ReservationMethodTransformer $reservationMethodTransformer,
        PaymentMethodTransformer $paymentMethodTransformer
    )
    {
        $this->dateRangeTransformer = $dateRangeTransformer;
        $this->noteTransformer = $noteTransformer;
        $this->questionGroupTransformer = $questionGroupTransformer;
        $this->hotelTransformer = $hotelTransformer;
        $this->locationTransformer = $locationTransformer;
        $this->clientTransformer = $clientTransformer;
        $this->plannerTransformer = $plannerTransformer;
        $this->attachmentTransformer = $attachmentTransformer;
        $this->tagTransformer = $tagTransformer;
        $this->reservationMethodTransformer = $reservationMethodTransformer;
        $this->paymentMethodTransformer = $paymentMethodTransformer;
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
        return (object)collect($proposalRequest)->only([
            'id' ,
            'client_id',
            'event_id',
            'planner_id',
            'specificity',
            'occupancy_per_room_typical',
            'occupancy_per_room_max',
            'room_nights_consumed_per_comp_request',
            'currency',
            'anticipated_attendance',
            'description',
        ])->merge([
            'created_by'    => [
                'name'  => data_get('name', $proposalRequest->author),
                'email' => data_get('email', $proposalRequest->author),
                'phone' => data_get('phone', $proposalRequest->author),
            ],
            'event_tags'                    => $this->tagTransformer->transformCollection($proposalRequest->event->tags),
            'licensee_timezone'             => $proposalRequest->event->licensee->timezone,
            'licensee_name'                 => $proposalRequest->event->licensee->company_name,
            'cutoff_date'                   => self::dateFormatOrNull($proposalRequest->cutoff_date, 'Y-m-d H:i:s'),
            'client'                        => $this->clientTransformer->transform($proposalRequest->client),
            'client_place_id'               => ($proposalRequest->client) ? $proposalRequest->client->place_id : null,
            'client_name'                   => ($proposalRequest->client) ? $proposalRequest->client->name : null,
            'planner_place_id'              => ($proposalRequest->planner) ? $proposalRequest->planner->place_id : null,
            'planner_name'                  => ($proposalRequest->planner) ? $proposalRequest->planner->name : null,
            'planner'                       => ($proposalRequest->planner) ? $this->plannerTransformer->transform($proposalRequest->planner) : null,
            'event_group_name'              => ($proposalRequest->event->eventGroup) ? $proposalRequest->event->eventGroup->name : null,
            'is_meeting_space_required'     => (bool)($proposalRequest->is_meeting_space_required),
            'is_food_and_beverage_required' => (bool)($proposalRequest->is_food_and_beverage_required),
            'is_attrition_acceptable' => (bool)($proposalRequest->is_attrition_acceptable),
            'is_visible_to_client'          => (bool)($proposalRequest->is_visible_to_client),
            'is_visible_to_planner'         => (bool)($proposalRequest->is_visible_to_planner),
            'event'                         => $proposalRequest->event->name,
            'event_type_id'                 => $proposalRequest->event->event_type_id,
            'event_type'                    => ($proposalRequest->event->event_type_id) ? $proposalRequest->event->type->name : null,
            'event_sub_type_id'             => $proposalRequest->event->event_sub_type_id,
            'event_sub_type'                => ($proposalRequest->event->event_sub_type_id) ? $proposalRequest->event->subType->name : null,
            'event_group_id'                => $proposalRequest->event->event_group_id,
            'commission'                    => number_format($proposalRequest->commission, 2),
            'date_ranges'                   => $this->dateRangeTransformer->transformCollection($proposalRequest->event->dateRanges()->orderBy('start_date', 'asc')->get()),
            'locations'                     => $this->locationTransformer->transformCollection($proposalRequest->eventLocations),
            'notes'                         => $this->noteTransformer->transformCollection($proposalRequest->notes),
            'question_groups'               => $this->questionGroupTransformer->transformCollection($proposalRequest->questionGroups),
            'hotels'                        => $this->getHotels($proposalRequest->id),
            'rebate'                        => number_format($proposalRequest->rebate, 2),
            'stage'                         => $proposalRequest->present()->stage,
            'reservation_methods'            => $this->reservationMethodTransformer->transformCollection($proposalRequest->reservationMethods),
            'payment_methods'            => $this->paymentMethodTransformer->transformCollection($proposalRequest->paymentMethods),
            'is_disbursed'                  => $proposalRequest->present()->is_disbursed,
            'created_at'                    => $proposalRequest->created_at->format('Y-m-d H:i:s'),
            'attachments'                   => ($proposalRequest->attachments) ? $this->attachmentTransformer->transformCollection($proposalRequest->attachments) : null,
        ])->toArray();
    }

    /**
     * Get hotels with recipient information
     *
     * @param int $requestId
     */
    private function getHotels($requestId)
    {
        $assembler = $this->assemble(
            new HotelsAndRecipientsForProposalRequestAssembler(
                $requestId
            )
        );

        return $assembler->get('hotels');
    }
}
