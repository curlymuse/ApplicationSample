<?php

namespace App\Transformers\RequestNote;

use App\Transformers\Transformer;

class RequestNoteTransformer extends Transformer
{
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
            'body'      => $object->body,
            'author'    => $object->author->name,
            'created_at'    => $object->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
