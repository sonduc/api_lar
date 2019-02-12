<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 1/18/2019
 * Time: 3:34 PM
 */

namespace App\Repositories\Topic;

use App\Repositories\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Repositories\SubTopic\SubTopic;

class Topic extends Entity
{
    use SoftDeletes;
    protected $table = 'topics';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable
        = [
            'name'
        ];

    /**
     * The attributes that are cast permission from json string to array
     * @var array
     */
    protected $casts = ['permissions' => 'array'];
    
    public function subs()
    {
        return $this->hasMany(SubTopic::class, 'topic_id');
    }
}
