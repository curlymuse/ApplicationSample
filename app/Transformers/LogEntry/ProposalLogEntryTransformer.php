<?php

namespace App\Transformers\LogEntry;

use App\Models\Proposal;
use App\Transformers\Hotel\HotelSimpleViewTransformer;
use App\Transformers\Transformer;

class ProposalLogEntryTransformer extends Transformer
{
    /**
     * @var LogEntryTransformer
     */
    private $parentTransformer;

    /**
     * ContractLogEntryTransformer constructor.
     * @param LogEntryTransformer $parentTransformer
     */
    public function __construct(
        LogEntryTransformer $parentTransformer
    )
    {
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
        $base = $this->parentTransformer->transform($object);

        return (object)collect($base)->merge([
//            'hotel' => $object->subject->hotel->name,
        ])->toArray();
    }
}