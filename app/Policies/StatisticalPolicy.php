<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StatisticalPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the statistical.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function view(User $user)
    {
        return $user->hasAccess(['statistical.view']);
    }

    /**
     * Determine whether the user can create statistical.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasAccess(['statistical.create']);
    }

    /**
     * Determine whether the user can update the statistical.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function update(User $user)
    {
        return $user->hasAccess(['statistical.update']);
    }

    /**
     * Determine whether the user can delete the statistical.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function delete(User $user)
    {
        return $user->hasAccess(['statistical.delete']);
    }
}
