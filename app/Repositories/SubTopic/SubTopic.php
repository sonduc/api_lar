<?php

namespace App\Repositories\SubTopic;

use App\Repositories\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubTopic extends Entity
{
    use SoftDeletes;
    protected $table = 'sub_topics';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable
        = [
           'name','topic_id'
        ];

    /**
     * The attributes that are cast permission from json string to array
     * @var array
     */
    protected $casts = ['permissions' => 'array'];
}
