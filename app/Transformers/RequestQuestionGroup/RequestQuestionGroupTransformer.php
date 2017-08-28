<?php

namespace App\Transformers\RequestQuestionGroup;

use App\Transformers\RequestQuestion\RequestQuestionTransformer;
use App\Transformers\Transformer;

class RequestQuestionGroupTransformer extends Transformer
{
    /**
     * @var RequestQuestionTransformer
     */
    private $questionTransformer;

    /**
     * RequestQuestionGroupTransformer constructor.
     * @param RequestQuestionTransformer $questionTransformer
     */
    public function __construct(RequestQuestionTransformer $questionTransformer)
    {
        $this->questionTransformer = $questionTransformer;
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
        return (object)[
            'id'      => $object->id,
            'name'      => $object->name,
            'questions'     => $this->questionTransformer->transformCollection($object->questions),
        ];
    }
}