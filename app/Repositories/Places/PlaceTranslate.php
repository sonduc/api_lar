<?php

namespace App\Repositories\Places;

use App\Repositories\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlaceTranslate extends Entity
{
    use PresentationTrait, FilterTrait, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','description','lang','place_id'
    ];

    /**
     * The attributes that are cast permission from json string to array
     * @var array
     */
    protected $casts = ['permissions' => 'array'];


}
