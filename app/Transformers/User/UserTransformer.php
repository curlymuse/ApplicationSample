<?php

namespace App\Transformers\User;

use App\Transformers\Role\RoleTransformer;
use App\Transformers\Transformer;

class UserTransformer extends Transformer
{
    /**
     * @var RoleTransformer
     */
    private $roleTransformer;

    /**
     * @param RoleTransformer $roleTransformer
     */
    public function __construct(RoleTransformer $roleTransformer)
    {
        $this->roleTransformer = $roleTransformer;
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
        $roles = [];
        foreach ($object->roles as $role) {
            $roles[] = $this->roleTransformer->transform($role);
        }

        return (object)[
            'id'        => $object->id,
            'name'      => $object->name,
            'roles'     => $roles,
            'email'      => $object->email,
            'phone'     => $object->phone,
            'position'     => $object->position,
            'is_claimed'    => (bool)($object->password),
        ];
    }
}