<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TransactionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the transaction.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function view(User $user)
    {
        return $user->hasAccess(['transaction.view']);
    }

    /**
     * Determine whether the user can create transaction.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasAccess(['transaction.create']);
    }

    /**
     * Determine whether the user can update the transaction.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function update(User $user)
    {
        return $user->hasAccess(['transaction.update']);
    }

    /**
     * Determine whether the user can delete the transaction.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function delete(User $user)
    {
        return $user->hasAccess(['transaction.delete']);
    }
}
