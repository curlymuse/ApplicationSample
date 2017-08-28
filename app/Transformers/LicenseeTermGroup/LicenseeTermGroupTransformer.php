<?php

namespace App\Transformers\LicenseeTermGroup;

use App\Transformers\LicenseeTerm\LicenseeTermTransformer;
use App\Transformers\Transformer;

class LicenseeTermGroupTransformer extends Transformer
{

    /**
     * @var LicenseeTermTransformer
     */
    private $termTransformer;

    /**
     * @param LicenseeTermTransformer $termTransformer
     */
    public function __construct(LicenseeTermTransformer $termTransformer)
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