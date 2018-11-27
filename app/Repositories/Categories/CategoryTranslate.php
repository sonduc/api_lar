<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 26/09/2018
 * Time: 09:37
 */

namespace App\Repositories\Categories;

use App\Repositories\Entity;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryTranslate extends Entity
{
    use PresentationTrait, FilterTrait, SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'categories_translate';

    protected $fillable
        = [
            'name','lang','category_id','slug'
        ];
}
