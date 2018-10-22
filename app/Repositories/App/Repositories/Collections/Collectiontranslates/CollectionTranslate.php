<?php

namespace App\Repositories\App\Repositories\Collections\CollectionTranslates;

use App\Repositories\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Collectiontranslate extends Entity
{
    use PresentationTrait, FilterTrait, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        
    ];

    /**
     * The attributes that are cast permission from json string to array
     * @var array
     */
    protected $casts = ['permissions' => 'array'];


}