<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 27/09/2018
 * Time: 17:20
 */

namespace App\Repositories\Blogs;

use App\Repositories\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;
class BlogTag extends Entity
{
    use PresentationTrait, FilterTrait, SoftDeletes;

    protected $table = 'blog_tags';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable
        = [
            'blog_id','tag_id'

        ];

    /**
     * The attributes that are cast permission from json string to array
     * @var array
     */
    protected $casts = ['permissions' => 'array'];

}
