<?php

namespace App\Transformers\User;

use App\Transformers\Transformer;

class AddedUserTransformer extends Transformer
{

    /**
     * @var UserTransformer
     */
    private $parentTransformer;

    /**
     * @param UserTransformer $parentTransformer
     */
    public function __construct(UserTransformer $parentTransformer)
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

        return (object)collect($base)->only([
            'id'        => $object->id,
            'email'     => $object->email,
        ])->merge([
            'type'      => $object->roles()->first()->name,
        ])->toArray();
    }
}