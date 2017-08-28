<?php

namespace App\Transformers\RequestQuestion;

use App\Transformers\Transformer;

class RequestQuestionTransformer extends Transformer
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
            'id'      => $object->id,
            'question'      => $object->question,
        ];
    }
}