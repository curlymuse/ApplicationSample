<?php

namespace App\Transformers\Event;

use App\Repositories\Contracts\TagRepositoryInterface;
use App\Transformers\Tag\TagTransformer;
use App\Transformers\Transformer;

class BasicEventTransformer extends Transformer
{
    /**
     * @var TagTransformer
     */
    private $tagTransformer;

    /**
     * BasicEventTransformer constructor.
     * @param TagTransformer $tagTransformer
     */
    public function __construct(
        TagTransformer $tagTransformer
    )
    {
        $this->tagTransformer = $tagTransformer;
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
            'id'    => $object->id,
            'group_id'  => $object->event_group_id,
            'name'  => $object->name,
            'tags'  => $this->tagTransformer->transformCollection($object->tags)
        ];
    }
}