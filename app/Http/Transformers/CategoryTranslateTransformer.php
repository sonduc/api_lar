<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 27/09/2018
 * Time: 08:48
 */

namespace App\Http\Transformers;

use App\Repositories\Categories\CategoryTranslate;
use League\Fractal\TransformerAbstract;
class CategoryTranslateTransformer extends TransformerAbstract
{
    protected $availableIncludes
        = [

        ];

    /**
     *
     * @param ComfortTranslate $comfort
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
            'lang'                  => $category->lang,
            'created_at'            => $category->created_at->format('Y-m-d H:i:s'),
            'updated_at'            => $category->updated_at->format('Y-m-d H:i:s'),
        ];
    }

}
