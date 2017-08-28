<?php

namespace App\Transformers\ProposalDateRange;

use App\Transformers\EventDateRange\SimpleDateRangeTransformer;
use App\Transformers\Transformer;
use App\Transformers\User\SimpleUserTransformer;

class ProposalDateRangeTransformer extends Transformer
{
    /**
     * @var SimpleUserTransformer
     */
    private $userTransformer;

    /**
     * @var SimpleDateRangeTransformer
     */
    private $dateRangeTransformer;

    /**
     * ProposalDateRangeTransformer constructor.
     * @param SimpleDateRangeTransformer $dateRangeTransformer
     * @param SimpleUserTransformer $userTransformer
     */
    public function __construct(
        SimpleDateRangeTransformer $dateRangeTransformer,
        SimpleUserTransformer $userTransformer
    )
    {
        $this->userTransformer = $userTransformer;
        $this->dateRangeTransformer = $dateRangeTransformer;
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
        $base = $this->dateRangeTransformer->transform($object->eventDateRange);

        return (object)collect($base)
            ->merge([
                'declined_because' => $object->declined_because,
                'declined_by_user_type' => $object->declined_by_user_type,
                'rooms'      => json_decode($object->rooms),
                'meeting_spaces'      => json_decode($object->meeting_spaces),
                'food_and_beverage_spaces'      => json_decode($object->food_and_beverage_spaces),
                'declined_at'   => self::dateFormatOrNull($object->declined_at, 'Y-m-d H:i:s'),
                'submitted_at'   => self::dateFormatOrNull($object->submitted_at, 'Y-m-d H:i:s'),
                'declined_by_user' => ($object->declined_by_user) ? $this->userTransformer->transform($object->userWhoDeclined) : null,
                'submitted_by_user' => ($object->submitted_by_user) ? $this->userTransformer->transform($object->userWhoSubmitted) : null,
                'status'        => $object->present()->status,
            ])->toArray();
    }
}