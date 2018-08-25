<?php

namespace App\Repositories\Languages;

use App\Repositories\Entity;

class Language extends Entity
{
    use PresentationTrait, FilterTrait;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * The attributes that are cast permission from json string to array
     * @var array
     */
    protected $casts = ['permissions' => 'array'];

    /**
     * Relationship with user
     * @return Relation
     */
    
}
