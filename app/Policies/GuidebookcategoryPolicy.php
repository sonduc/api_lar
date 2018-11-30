<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GuidebookCategoryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the guidebookcategory.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function view(User $user)
    {
        return $user->hasAccess(['guidebookcategory.view']);
    }

    /**
     * Determine whether the user can create guidebookcategory.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasAccess(['guidebookcategory.create']);
    }

    /**
     * Determine whether the user can update the guidebookcategory.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function update(User $user)
    {
        return $user->hasAccess(['guidebookcategory.update']);
    }

    /**
     * Determine whether the user can delete the guidebookcategory.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function delete(User $user)
    {
        return $user->hasAccess(['guidebookcategory.delete']);
    }
}
