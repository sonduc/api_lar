<?php

namespace App\Policies;

use App\User;
use App\Repositories\Roles\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the role.
     *
     * @param  \App\User  $user
     * @param  \App\Repositories\Categories\Role  $role
     * @return mixed
     */
    public function view(User $user, Role $role = null)
    {
        return $user->hasAccess(['role.view']);
    }

    /**
     * Determine whether the user can create role.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasAccess(['role.create']);
    }

    /**
     * Determine whether the user can update the role.
     *
     * @param  \App\User  $user
     * @param  \App\Repositories\Categories\Role  $role
     * @return mixed
     */
    public function update(User $user, Role $role = null)
    {
        return $user->hasAccess(['role.update']);
    }

    /**
     * Determine whether the user can delete the role.
     *
     * @param  \App\User  $user
     * @param  \App\Repositories\Categories\Role  $role
     * @return mixed
     */
    public function delete(User $user, Role $role = null)
    {
        return $user->hasAccess(['role.delete']);
    }
}
