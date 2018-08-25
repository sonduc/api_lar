<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the _user.
     *
     * @param  \App\User  $user
     * @param  \App\Repositories\Categories\User  $_user
     * @return mixed
     */
    public function view(User $user, User $_user = null)
    {
        return $user->hasAccess(['_user.view']);
    }

    /**
     * Determine whether the user can create _user.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasAccess(['_user.create']);
    }

    /**
     * Determine whether the user can update the _user.
     *
     * @param  \App\User  $user
     * @param  \App\Repositories\Categories\User  $_user
     * @return mixed
     */
    public function update(User $user, User $_user = null)
    {
        return $user->hasAccess(['_user.update']);
    }

    /**
     * Determine whether the user can delete the _user.
     *
     * @param  \App\User  $user
     * @param  \App\Repositories\Categories\User  $_user
     * @return mixed
     */
    public function delete(User $user, User $_user = null)
    {
        return $user->hasAccess(['_user.delete']);
    }
}
