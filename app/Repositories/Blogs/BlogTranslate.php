<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 27/09/2018
 * Time: 13:27
 */

namespace App\Repositories\Blogs;

use App\Repositories\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;
class BlogTranslate extends  Entity
{
    use PresentationTrait, FilterTrait, SoftDeletes;

    protected $table = 'blog_translates';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable
        = [
            'title','slug','content','blog_id','lang'
        ];

    /**
     * The attributes that are cast permission from json string to array
     * @var array
     */
    protected $casts = ['permissions' => 'array'];

}
