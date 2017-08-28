<?php

namespace App\Transformers\Brand;

use App\Transformers\Transformer;

class ParentBrandTransformer extends Transformer
{

    /**
     * @var SubBrandTransformer
     */
    private $subBrandTransformer;

    /**
     * @param SubBrandTransformer $subBrandTransformer
     */
    public function __construct(SubBrandTransformer $subBrandTransformer)
    {
        $this->subBrandTransformer = $subBrandTransformer;
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
        $base = $this->subBrandTransformer->transform($object);

        return (object)collect($base)->merge([
            'subBrands' => $this->transformCollection($object->subBrands),
        ])->toArray();
    }
}
