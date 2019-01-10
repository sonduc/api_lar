<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 09/01/2019
 * Time: 16:05
 */

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BaoKimTradePolicy
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
        return $user->hasAccess(['baokimTrade.view']);
    }

}
