<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ComfortPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the comfort.
     *
     * @param  \App\User $user
     *
     * @return mixed
     */
    public function view(User $user)
    {
        return $user->hasAccess(['comfort.view']);
    }

    /**
     * Determine whether the user can create comfort.
     *
     * @param  \App\User $user
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasAccess(['comfort.create']);
    }

    /**
     * Determine whether the user can update the comfort.
     *
     * @param  \App\User $user
     *
     * @return mixed
     */
    public function update(User $user)
    {
        return $user->hasAccess(['comfort.update']);
    }

    /**
     * Determine whether the user can delete the comfort.
     *
     * @param  \App\User $user
     *
     * @return mixed
     */
    public function delete(User $user)
    {
        return $user->hasAccess(['comfort.delete']);
    }
}
