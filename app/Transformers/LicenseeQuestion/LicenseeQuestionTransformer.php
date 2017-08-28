<?php

namespace App\Transformers\LicenseeQuestion;

use App\Transformers\Transformer;

class LicenseeQuestionTransformer extends Transformer
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
        return (object)[
            'id'        => $object->id,
            'question'      => $object->question,
        ];
    }
}