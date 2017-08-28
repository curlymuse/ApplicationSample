<?php

namespace App\Repositories\Contracts;

interface RoleRepositoryInterface
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
    public function addRoleToUser($user, $slug, $rolable = null);

    /**
     * Pull an array that maps slug to name for all roles
     *
     * @return mixed
     */
    public function getRoleList();
}
