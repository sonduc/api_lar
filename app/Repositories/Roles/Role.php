<?php

namespace App\Repositories\Roles;

use App\Repositories\Entity;

class Role extends Entity
{
    use PresentationTrait, FilterTrait;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'slug', 'permissions'];

    /**
     * The attributes that are cast permission from json string to array
     * @var array
     */
    protected $casts = ['permissions' => 'array'];

    /**
     * Relationship with user
     * @return Relation
     */
    public function users()
    {
        return $this->belongsToMany(\App\User::class, 'role_users');
    }
}
