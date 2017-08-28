<?php

namespace App\Transformers\Attachment;

use App\Transformers\Transformer;
use App\Transformers\User\SimpleUserTransformer;
use Illuminate\Support\Collection;

class AttachmentTransformer extends Transformer
{

    /**
     * @var SimpleUserTransformer
     */
    private $userTransformer;

    /**
     * @param SimpleUserTransformer $userTransformer
     */
    public function __construct(SimpleUserTransformer $userTransformer)
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
            'id',
            'url',
            'display_name',
            'category',
        ])->merge([
            'uploaded_by_user'  => $this->userTransformer->transform($object->user),
        ])->toArray();
    }

    public function transformCollection(Collection $objects)
    {
        $categorized = [];
        $objects->each(function($object) use (&$categorized) {
            if (! isset($categorized[$object->category])) {
                $categorized[$object->category] = [];
            }
            $categorized[$object->category][] = $this->transform($object);
        });

        return $categorized;
    }
}
