<?php

namespace App\Transformers\LicenseeQuestionGroup;

use App\Transformers\LicenseeQuestion\LicenseeQuestionTransformer;
use App\Transformers\Transformer;

class LicenseeQuestionGroupTransformer extends Transformer
{
    /**
     * @var LicenseeQuestionTransformer
     */
    private $questionTransformer;

    /**
     * LicenseeQuestionGroupTransformer constructor.
     * @param LicenseeQuestionTransformer $questionTransformer
     */
    public function __construct(LicenseeQuestionTransformer $questionTransformer)
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
            'id'        => $object->id,
            'name'      => $object->name,
            'questions' => $this->questionTransformer->transformCollection($object->questions)
        ];
    }
}