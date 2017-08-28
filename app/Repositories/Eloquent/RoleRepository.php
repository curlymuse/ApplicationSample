<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Repositories\Contracts\User;

class RoleRepository extends Repository implements RoleRepositoryInterface
{
    /**
     * Add a role to the user
     *
     * @param User $user
     * @param string $slug
     * @param null $rolable
     *
     * @return mixed
     */
    public function addRoleToUser($user, $slug, $rolable = null)
    {
        $role = $this->findWhere(['slug' => $slug]);

        if (! $role) {
            return false;
        }

        $rolableInfo = [];

        if ($rolable) {
            $rolableInfo = [
                'rolable_type'  => get_class($rolable),
                'rolable_id'    => $rolable->id,
            ];
        }

        $user->roles()->attach($role, $rolableInfo);
    }

    /**
     * Pull an array that maps slug to name for all roles
     *
     * @return array
     */
    public function getRoleList()
    {
        return $this->model
            ->get()
            ->pluck('name', 'slug')
            ->toArray();
    }
}
