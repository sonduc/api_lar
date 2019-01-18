<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 1/17/2019
 * Time: 3:54 AM
 */

namespace App\Policies;


use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SeoPolicy
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
        return $user->hasAccess(['seo.view']);
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
        return $user->hasAccess(['seo.create']);
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
        return $user->hasAccess(['seo.update']);
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
        return $user->hasAccess(['seo.delete']);
    }

}