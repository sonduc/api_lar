<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 27/09/2018
 * Time: 08:48
 */

namespace App\Http\Transformers\Customer;

use App\Repositories\Categories\CategoryTranslate;
use League\Fractal\TransformerAbstract;

class CategoryTranslateTransformer extends TransformerAbstract
{
    protected $availableIncludes
        = [

        ];

    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param CategoryTranslate $category
     *
     * @return array
     */
    public function transform(CategoryTranslate $category)
    {
        if (is_null($category)) {
            return [];
        }

        return [
            'id'                    => $category->id,
            'category_id'           => $category->category_id,
            'name'                  => $category->name,
            'slug'                  => $category->slug,
            'lang'                  => $category->lang
        ];
    }
}
