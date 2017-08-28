<?php

namespace App\Transformers\Licensee;

use App\Transformers\Transformer;

class LicenseeTransformer extends Transformer
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
        return (object) [
            'id'                => $object->id,
            'company_name'      => $object->company_name,
            'is_suspended'      => $object->is_suspended,
            'timezone'          => $object->timezone,
            'created_at'        => $object->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
