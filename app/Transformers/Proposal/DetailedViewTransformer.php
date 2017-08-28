<?php

namespace App\Transformers\Proposal;

use App\Transformers\Attachment\AttachmentTransformer;
use App\Transformers\EventDateRange\SimpleDateRangeTransformer;
use App\Transformers\Hotel\HotelTransformer;
use App\Transformers\ProposalDateRange\ProposalDateRangeTransformer;
use App\Transformers\ProposalRequest\ProposalRequestSimpleTransformer;
use App\Transformers\Transformer;
use App\Transformers\User\SimpleUserTransformer;

class DetailedViewTransformer extends Transformer
{
    /**
     * @var ProposalSimpleViewTransformer
     */
    private $simpleViewTransformer;

    /**
     * @var SimpleDateRangeTransformer
     */
    private $dateRangeTransformer;

    /**
     * @var SimpleUserTransformer
     */
    private $userTransformer;

    /**
     * @var ProposalDateRangeTransformer
     */
    private $proposalDateRangeTransformer;

    /**
     * @var HotelTransformer
     */
    private $hotelTransformer;

    /**
     * @var AttachmentTransformer
     */
    private $attachmentTransformer;

    /**
     * @var ProposalRequestSimpleTransformer
     */
    private $requestTransformer;

    /**
     * DetailedViewTransformer constructor.
     * @param ProposalSimpleViewTransformer $simpleViewTransformer
     * @param SimpleDateRangeTransformer $dateRangeTransformer
     * @param SimpleUserTransformer $userTransformer
     * @param ProposalDateRangeTransformer $proposalDateRangeTransformer
     * @param HotelTransformer $hotelTransformer
     * @param AttachmentTransformer $attachmentTransformer
     * @param ProposalRequestSimpleTransformer $requestTransformer
     */
    public function __construct(
        ProposalSimpleViewTransformer $simpleViewTransformer,
        SimpleDateRangeTransformer $dateRangeTransformer,
        SimpleUserTransformer $userTransformer,
        ProposalDateRangeTransformer $proposalDateRangeTransformer,
        HotelTransformer $hotelTransformer,
        AttachmentTransformer $attachmentTransformer,
        ProposalRequestSimpleTransformer $requestTransformer
    )
    {
        $this->simpleViewTransformer = $simpleViewTransformer;
        $this->dateRangeTransformer = $dateRangeTransformer;
        $this->userTransformer = $userTransformer;
        $this->proposalDateRangeTransformer = $proposalDateRangeTransformer;
        $this->hotelTransformer = $hotelTransformer;
        $this->attachmentTransformer = $attachmentTransformer;
        $this->requestTransformer = $requestTransformer;
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
        $base = $this->simpleViewTransformer->transform($object);

        return (object)(collect($base)->merge(
            collect($object)->only([
                'proposal_request_id',
                'commission',
                'rebate',
                'additional_charge_per_adult',
                'attrition_rate',
                'tax_rate',
                'min_age_to_check_in',
                'additional_fees',
                'additional_fees_units',
                'honor_bid_until',
                'min_length_of_stay',
                'deposit_policy',
                'cancellation_policy',
                'cancellation_policy_days',
                'cancellation_policy_file',
                'notes',
                'questions',
            ])->merge([
                'request'       => $this->requestTransformer->transform($object->proposalRequest),
                'hotel'        => $this->hotelTransformer->transform($object->hotel),
                'has_contract' => ($object->contracts()->count() > 0),
                'date_ranges'  => $this->proposalDateRangeTransformer->transformCollection($object->dateRanges),
                'notes'        => json_decode($object->notes),
                'questions'    => json_decode($object->questions),
                'attachments'  => ($object->attachments) ? $this->attachmentTransformer->transformCollection($object->attachments) : null,
                'hotel_recipients'  => $this->userTransformer->transformCollection(
                    $object
                        ->proposalRequest
                        ->requestHotels()
                        ->whereHotelId($object->hotel_id)
                        ->first()
                        ->users
                )
            ])->toArray())
        )->toArray();
    }
}
