<?php

namespace App\Policies;

use App\Repositories\Rooms\Room;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RoomPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the role.
     *
     * @param  \App\User                         $user
     * @param  \App\Repositories\Categories\Role $role
     *
     * @return mixed
     */
    public function view(User $user,$id = [])
    {
        return  $user->checkOwner($user,$this->getRoomResource($id)) == false? $user->checkOwner($user,$this->getRoomResource($id)) : $user->hasAccess(['room.view']);
    }

    /**
     * Determine whether the user can  create role.
     *
     * @param  \App\User $user
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasAccess(['room.create']);
    }

    /**
     * Determine whether the user can update the role.
     *
     * @param  \App\User                         $user
     * @param  \App\Repositories\Categories\Role $role
     *
     * @return mixed
     */
    public function update(User $user, $id = [])
    {
        return  $user->checkOwner($user,$this->getRoomResource($id)) == false? $user->checkOwner($user,$this->getRoomResource($id)) : $user->hasAccess(['room.update']);
    }

    /**
     * Determine whether the user can delete the role.
     *
     * @param  \App\User                         $user
     * @param  \App\Repositories\Categories\Role $role
     *
     * @return mixed
     */
    public function delete(User $user,$id)
    {
        return  $user->checkOwner($user,$this->getRoomResource($id)) == false? $user->checkOwner($user,$this->getRoomResource($id)) : $user->hasAccess(['room.delete']);
    }

    /**
     *
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $id
     */
    public function getRoomResource($id= [])
    {
        return Room::findOrFail($id)->toArray();

    }

}
