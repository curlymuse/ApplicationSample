<?php

namespace App\Transformers\ContractTermGroup;

use App\Transformers\ContractTerm\ContractTermTransformer;
use App\Transformers\Transformer;

class ContractTermGroupTransformer extends Transformer
{

    /**
     * @var LicenseeTermTransformer
     */
    private $termTransformer;

    /**
     * @param LicenseeTermTransformer $termTransformer
     */
    public function __construct(ContractTermTransformer $termTransformer)
    {
        $this->termTransformer = $termTransformer;
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
            'name',
        ])->merge([
            'terms' => $this->termTransformer->transformCollection($object->terms),
        ])->toArray();
    }
}