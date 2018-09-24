<?php

namespace App\Repositories\Blogs;

use App\Repositories\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blog extends Entity
{
    use PresentationTrait, FilterTrait, SoftDeletes;

    protected $table = 'blogs';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable
        = [

        ];

    /**
     * The attributes that are cast permission from json string to array
     * @var array
     */
    protected $casts = ['permissions' => 'array'];


}
