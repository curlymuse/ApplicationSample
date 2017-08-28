<?php

namespace App\Transformers\Contract;

use App\Transformers\Attachment\AttachmentTransformer;
use App\Transformers\ContractTermGroup\ContractTermGroupTransformer;
use App\Transformers\EventDateRange\EventDateRangeTransformer;
use App\Transformers\EventLocation\EventLocationTransformer;
use App\Transformers\Hotel\HotelSimpleViewTransformer;
use App\Transformers\Hotel\HotelTransformer;
use App\Transformers\PaymentMethod\PaymentMethodTransformer;
use App\Transformers\ProposalRequest\ProposalRequestSimpleTransformer;
use App\Transformers\ReservationMethod\ReservationMethodTransformer;
use App\Transformers\RoomSet\RoomSetTransformer;
use App\Transformers\Transformer;
use App\Transformers\User\UserTransformer;

class ContractTransformer extends Transformer
{
    /**
     * @var RoomSetTransformer
     */
    private $roomSetTransformer;

    /**
     * @var EventDateRangeTransformer
     */
    private $dateRangeTransformer;

    /**
     * @var HotelSimpleViewTransformer
     */
    private $hotelTransformer;

    /**
     * @var ContractTermGroupTransformer
     */
    private $termGroupTransformer;

    /**
     * @var AttachmentTransformer
     */
    private $attachmentTransformer;

    /**
     * @var EventLocationTransformer
     */
    private $locationTransformer;

    /**
     * @var UserTransformer
     */
    private $userTransformer;

    /**
     * @var ProposalRequestSimpleTransformer
     */
    private $requestTransformer;

    /**
     * @var ReservationMethodTransformer
     */
    private $reservationMethodTransformer;

    /**
     * @var PaymentMethodTransformer
     */
    private $paymentMethodTransformer;

    /**
     * ContractTransformer constructor.
     *
     * @param RoomSetTransformer $roomSetTransformer
     * @param EventDateRangeTransformer $dateRangeTransformer
     * @param HotelSimpleViewTransformer|HotelTransformer $hotelTransformer
     * @param ContractTermGroupTransformer $termGroupTransformer
     * @param EventLocationTransformer $locationTransformer
     * @param AttachmentTransformer $attachmentTransformer
     * @param UserTransformer $userTransformer
     * @param ProposalRequestSimpleTransformer $requestTransformer
     * @param ReservationMethodTransformer $reservationMethodTransformer
     * @param PaymentMethodTransformer $paymentMethodTransformer
     */
    public function __construct(
        RoomSetTransformer $roomSetTransformer,
        EventDateRangeTransformer $dateRangeTransformer,
        HotelTransformer $hotelTransformer,
        ContractTermGroupTransformer $termGroupTransformer,
        EventLocationTransformer $locationTransformer,
        AttachmentTransformer $attachmentTransformer,
        UserTransformer $userTransformer,
        ProposalRequestSimpleTransformer $requestTransformer,
        ReservationMethodTransformer $reservationMethodTransformer,
        PaymentMethodTransformer $paymentMethodTransformer
    )
    {
        $this->roomSetTransformer = $roomSetTransformer;
        $this->dateRangeTransformer = $dateRangeTransformer;
        $this->hotelTransformer = $hotelTransformer;
        $this->termGroupTransformer = $termGroupTransformer;
        $this->attachmentTransformer = $attachmentTransformer;
        $this->locationTransformer = $locationTransformer;
        $this->userTransformer = $userTransformer;
        $this->requestTransformer = $requestTransformer;
        $this->reservationMethodTransformer = $reservationMethodTransformer;
        $this->paymentMethodTransformer = $paymentMethodTransformer;
    }

    /**
     * Transform a single object
     *
     * @param $object
     *
     * @return mixed
     */
    public function transform($object)
    {
        return (object)collect($object)->only([
            'id',
            'is_client_owned',
            'is_offline_contract',
            'declined_by_hotel_because',
            'declined_by_owner_because',
            'is_meeting_space_required',
            'is_food_and_beverage_required',
            'commission',
            'rebate',
            'additional_charge_per_adult',
            'tax_rate',
            'min_age_to_check_in',
            'min_length_of_stay',
            'additional_fees',
            'additional_fees_units',
            'cutoff_date',
            'deposit_policy',
            'attrition_rate',
            'cancellation_policy',
            'cancellation_policy_days',
            'cancellation_policy_file',
            'notes',
            'questions',
            'room_breakdown',
            'proposal_id',
            'accepted_by_owner_signature',
            'accepted_by_hotel_signature',
        ])->merge([
            'request'       => $this->requestTransformer->transform($object->proposal->proposalRequest),
            'declined_by_hotel_user' => ($object->declined_by_hotel_user) ? $this->userTransformer->transform($object->hotelUserWhoDeclined) : null,
            'accepted_by_hotel_user' => ($object->accepted_by_hotel_user) ? $this->userTransformer->transform($object->hotelUserWhoAccepted) : null,
            'declined_by_owner_user' => ($object->declined_by_owner_user) ? $this->userTransformer->transform($object->ownerUserWhoDeclined) : null,
            'accepted_by_owner_user' => ($object->accepted_by_owner_user) ? $this->userTransformer->transform($object->ownerUserWhoAccepted) : null,
            'start_date' => static::dateFormatOrNull($object->start_date, 'Y-m-d'),
            'end_date' => static::dateFormatOrNull($object->end_date, 'Y-m-d'),
            'check_in_date' => static::dateFormatOrNull($object->check_in_date, 'Y-m-d'),
            'check_out_date' => static::dateFormatOrNull($object->check_out_date, 'Y-m-d'),
            'hotel'         => $this->hotelTransformer->transform($object->proposal->hotel),
            'licensee'      => $object->proposal->proposalRequest->event->licensee->company_name,
            'created_at'    => $object->created_at->format('Y-m-d H:i:s'),
            'proposal_request_id'  => $object->proposal->proposalRequest->id,
            'date_range'    => $this->dateRangeTransformer->transform($object->dateRange),
            'event_id'  => $object->proposal->proposalRequest->event_id,
            'event'  => $object->proposal->proposalRequest->event->name,
            'event_description'  => $object->proposal->proposalRequest->description,
            'event_type'  => ($object->proposal->proposalRequest->event_type_id) ? $object->proposal->proposalRequest->event->type->name : null,
            'event_sub_type'  => ($object->proposal->proposalRequest->event->event_sub_type_id) ? $object->proposal->proposalRequest->event->subType->name : null,
            'declined_by_hotel_at' => static::dateFormatOrNull($object->declined_by_hotel_at, 'Y-m-d H:i:s'),
            'declined_by_owner_at' => static::dateFormatOrNull($object->declined_by_owner_at, 'Y-m-d H:i:s'),
            'accepted_by_hotel_at' => static::dateFormatOrNull($object->accepted_by_hotel_at, 'Y-m-d H:i:s'),
            'accepted_by_owner_at' => static::dateFormatOrNull($object->accepted_by_owner_at, 'Y-m-d H:i:s'),
            'notes'    => json_decode($object->notes),
            'questions'    => static::groupQuestions(json_decode($object->questions)),
            'meeting_spaces'    => json_decode($object->meeting_spaces),
            'food_and_beverage'    => json_decode($object->food_and_beverage),
            'room_sets'     => $this->roomSetTransformer->transformCollection($object->roomSets),
            'term_groups'   => $this->termGroupTransformer->transformCollection($object->termGroups),
            'locations'   => $this->locationTransformer->transformCollection($object->proposal->proposalRequest->eventLocations),
            'reservation_methods'            => $this->reservationMethodTransformer->transformCollection($object->reservationMethods),
            'payment_methods'            => $this->paymentMethodTransformer->transformCollection($object->paymentMethods),
            'attachments'   => $this->attachmentTransformer->transformCollection(
                $object->attachments()->where('category', 'NOT LIKE', 'temp:%')->get()
            ),
            'status'    => $object->present()->status,
            'has_pending_change_orders' => $object
                ->changeOrders()
                ->whereNull('parent_id')
                ->whereHas('children', function($query) {
                    $query->whereNull('declined_at')
                        ->whereNull('accepted_at');
                })->exists(),
        ])->toArray();
    }

    /**
     * Group questions together by group name
     *
     * @param array $questions
     *
     * @return array
     */
    private static function groupQuestions($questions)
    {
        $grouped = [];

        $questions = collect($questions);

        foreach ($questions->pluck('group')->unique() as $group) {
            $grouped[] = [
                'group' => $group,
                'questions' => $questions
                    ->where('group', $group)
                    ->toArray(),
            ];
        }

        return $grouped;
    }
}
