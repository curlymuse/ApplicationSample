<?php

namespace App\Transformers\LogEntry;

use App\Transformers\Transformer;
use App\Transformers\User\SimpleUserTransformer;

class LogEntryTransformer extends Transformer
{

    /**
     * @var SimpleUserTransformer
     */
    private $userTransformer;

    /**
     * @param SimpleUserTransformer $userTransformer
     */
    public function __construct(
        SimpleUserTransformer $userTransformer
    )
    {
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
        return (object)collect($object)->only([
            'action',
            'description',
            'notes',
        ])->merge([
            'user'  => $this->userTransformer->transform($object->user),
            'datetime'  => $object->created_at->format('Y-m-d H:i:s'),
        ])->toArray();
    }
}