<?php

namespace App\Transformers\ProposalRequest;

use App\Transformers\Transformer;

class ProposalRequestSimpleTransformer extends Transformer
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
        return (object)collect($object)->only([
            'id' ,
            'specificity',
            'occupancy_per_room_typical',
            'occupancy_per_room_max',
            'room_nights_consumed_per_comp_request',
            'currency',
            'anticipated_attendance',
            'description',
        ])->merge([
            'cutoff_date'                   => self::dateFormatOrNull($object->cutoff_date, 'Y-m-d'),
            'rebate'                        => number_format($object->rebate, 2),
            'commission'                    => number_format($object->commission, 2),
            'licensee_timezone'             => $object->event->licensee->timezone,
            'licensee_name'                 => $object->event->licensee->company_name,
        ])->toArray();
    }
}