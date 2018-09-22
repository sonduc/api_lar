<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BookingPolicy
{
    use HandlesAuthorization;
    
    /**
     * Determine whether the user can view the booking.
     *
     * @param  \App\User $user
     *
     * @return mixed
     */
    public function view(User $user)
    {
        return $user->hasAccess(['booking.view']);
    }
    
    /**
     * Determine whether the user can create booking.
     *
     * @param  \App\User $user
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasAccess(['booking.create']);
    }
    
    /**
     * Determine whether the user can update the booking.
     *
     * @param  \App\User $user
     *
     * @return mixed
     */
    public function update(User $user)
    {
        return $user->hasAccess(['booking.update']);
    }
    
    /**
     * Determine whether the user can delete the booking.
     *
     * @param  \App\User $user
     *
     * @return mixed
     */
    public function delete(User $user)
    {
        return $user->hasAccess(['booking.delete']);
    }
}
