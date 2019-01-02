<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CheckingPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the checking.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function view(User $user)
    {
        return $user->hasAccess(['checking.view']);
    }

    /**
     * Determine whether the user can create checking.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasAccess(['checking.create']);
    }

    /**
     * Determine whether the user can update the checking.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function update(User $user)
    {
        return $user->hasAccess(['checking.update']);
    }

    /**
     * Determine whether the user can delete the checking.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function delete(User $user)
    {
        return $user->hasAccess(['checking.delete']);
    }
}
