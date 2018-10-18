<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 17/10/2018
 * Time: 13:40
 */

namespace App\Repositories\Collections;

use App\Repositories\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;
class CollectionTranslate extends Entity
{
    use PresentationTrait, FilterTrait, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id','collection_id','name','lang'
    ];

    /**
     * The attributes that are cast permission from json string to array
     * @var array
     */
    protected $casts = ['permissions' => 'array'];
}
