<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PromotionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the promotion.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function view(User $user)
    {
        return $user->hasAccess(['promotion.view']);
    }

    /**
     * Determine whether the user can create promotion.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasAccess(['promotion.create']);
    }

    /**
     * Determine whether the user can update the promotion.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function update(User $user)
    {
        return $user->hasAccess(['promotion.update']);
    }

    /**
     * Determine whether the user can delete the promotion.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function delete(User $user)
    {
        return $user->hasAccess(['promotion.delete']);
    }
}
