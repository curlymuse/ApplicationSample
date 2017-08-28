<?php

namespace App\Transformers\EventType;

use App\Transformers\Transformer;

class EventTypeTransformer extends Transformer
{
    /**
     * @var EventSubTypeTransformer
     */
    private $subTypeTransformer;

    /**
     * EventTypeTransformer constructor.
     * @param EventSubTypeTransformer $subTypeTransformer
     */
    public function __construct(EventSubTypeTransformer $subTypeTransformer)
    {
        $this->subTypeTransformer = $subTypeTransformer;
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
            'icon'      => $object->icon,
            'sub_types' => $this->subTypeTransformer->transformCollection($object->children)
        ];
    }
}