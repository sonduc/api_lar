<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 1/28/2019
 * Time: 2:58 PM
 */

namespace App\Policies;


use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class HostReviewPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the blog.
     *
     * @param  \App\User $user
     *
     * @return mixed
     */
    public function view(User $user)
    {
        return $user->hasAccess(['hostReview.view']);
    }

    /**
     * Determine whether the user can create blog.
     *
     * @param  \App\User $user
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasAccess(['hostReview.create']);
    }

    /**
     * Determine whether the user can update the blog.
     *
     * @param  \App\User $user
     *
     * @return mixed
     */
    public function update(User $user)
    {
        return $user->hasAccess(['hostReview.update']);
    }

    /**
     * Determine whether the user can delete the blog.
     *
     * @param  \App\User $user
     *
     * @return mixed
     */
    public function delete(User $user)
    {
        return $user->hasAccess(['hostReview.delete']);
    }

}