<?php

namespace App\Traits;

use Laravel\Spark\Spark;
use App\Repositories\Contracts\RoleRepositoryInterface;

trait HasRoles
{
    /**
     * Boot method for the trait.
     * Sets the Spark roles.
     *
     * @return void
     */
    public static function bootHasRoles()
    {
        $roleRepo = app(RoleRepositoryInterface::class);
        Spark::useRoles($roleRepo->getRoleList());
    }
}
