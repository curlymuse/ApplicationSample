<?php

namespace App\Transformers\ChangeOrder;

use App\Transformers\Transformer;
use App\Transformers\User\SimpleUserTransformer;

class ChangeOrderSetTransformer extends Transformer
{
    /**
     * @var ChangeOrderTransformer
     */
    private $changeOrderTransformer;

    /**
     * @var SimpleUserTransformer
     */
    private $userTransformer;

    /**
     * ChangeOrderSetTransformer constructor.
     * @param ChangeOrderTransformer $changeOrderTransformer
     * @param SimpleUserTransformer $userTransformer
     */
    public function __construct(
        ChangeOrderTransformer $changeOrderTransformer,
        SimpleUserTransformer $userTransformer
    )
    {
        $this->changeOrderTransformer = $changeOrderTransformer;
        $this->userTransformer = $userTransformer;
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
        $respondedByUser = ($object->present()->responded_by_user)
            ? $this->userTransformer->transform($object->present()->responded_by_user)
            : null;

        return (object)collect($object)->only([
            'id',
            'contract_id',
            'initiated_by_party',
            'declined_because',
            'reason',
        ])->merge([
            'num_changes'   => $object->children->count(),
            'num_accepted'  => $object->children()->whereNotNull('accepted_at')->count(),
            'num_declined'  => $object->children()->whereNotNull('declined_at')->count(),
            'responded_by_user' => $respondedByUser,
            'responded_at'  => self::dateFormatOrNull($object->present()->responded_at, 'Y-m-d H:i:s'),
            'initiated_by_user' => $this->userTransformer->transform($object->initiatedByUser),
            'changes'   => $this->changeOrderTransformer->transformCollection($object->children),
            'created_at'    => $object->created_at->format('Y-m-d H:i:s'),
        ])->toArray();
    }
}