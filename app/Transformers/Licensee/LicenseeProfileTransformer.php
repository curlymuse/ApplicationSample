<?php

namespace App\Transformers\Licensee;

use App\Transformers\Transformer;

class LicenseeProfileTransformer extends Transformer
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
            'default_rebate',
            'default_commission',
            'default_currency',
            'receive_daily_recap',
            'fax',
            'phone',
            'timezone',
            'country_code',
            'region',
            'city',
            'address',
            'org_type',
            'dba',
            'legal_name',
            'logo',
        ]);
    }
}