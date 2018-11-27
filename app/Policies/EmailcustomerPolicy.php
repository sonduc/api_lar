<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmailCustomerPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the emailcustomer.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function view(User $user)
    {
        return $user->hasAccess(['emailcustomer.view']);
    }

    /**
     * Determine whether the user can create emailcustomer.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasAccess(['emailcustomer.create']);
    }

    /**
     * Determine whether the user can update the emailcustomer.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function update(User $user)
    {
        return $user->hasAccess(['emailcustomer.update']);
    }

    /**
     * Determine whether the user can delete the emailcustomer.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function delete(User $user)
    {
        return $user->hasAccess(['emailcustomer.delete']);
    }
}
