<?php

namespace App\Transformers\EventGroup;

use App\Transformers\Event\BasicEventTransformer;
use App\Transformers\Transformer;

class EventGroupTransformer extends Transformer
{
    /**
     * @var BasicEventTransformer
     */
    private $eventTransformer;

    /**
     * EventGroupTransformer constructor.
     * @param BasicEventTransformer $eventTransformer
     */
    public function __construct(BasicEventTransformer $eventTransformer)
    {
        $this->eventTransformer = $eventTransformer;
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
        $events = [];
        foreach ($object->events as $event) {
            $events[] = $this->eventTransformer->transform($event);
        }

        return (object)[
            'id'        => $object->id,
            'client_id' => $object->client_id,
            'name'      => $object->name,
            'events'    => $events,
        ];
    }
}