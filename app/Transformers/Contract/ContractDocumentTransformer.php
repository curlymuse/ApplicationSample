<?php

namespace App\Transformers\Contract;

use App\Transformers\ChangeOrder\ChangeOrderSetTransformer;
use App\Transformers\Transformer;

class ContractDocumentTransformer extends Transformer
{
    /**
     * @var ChangeOrderSetTransformer
     */
    private $changeOrderTransformer;

    /**
     * @var ContractTransformer
     */
    private $parentTransformer;

    /**
     * ContractDocumentTransformer constructor.
     * @param ChangeOrderSetTransformer $changeOrderTransformer
     * @param ContractTransformer $parentTransformer
     */
    public function __construct(
        ChangeOrderSetTransformer $changeOrderTransformer,
        ContractTransformer $parentTransformer
    )
    {
        $this->changeOrderTransformer = $changeOrderTransformer;
        $this->parentTransformer = $parentTransformer;
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
        $base = ((bool)$object->snapshot)
            ? json_decode($object->snapshot)
            : $this->parentTransformer->transform($object);

        return (object)collect($base)->merge([
            'addendums' => $this->changeOrderTransformer->transformCollection($object->addendums()),
        ])->toArray();
    }
}