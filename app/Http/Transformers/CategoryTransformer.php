<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 26/09/2018
 * Time: 15:36
 */

namespace App\Http\Transformers;

use App\Http\Transformers\Traits\FilterTrait;
use App\Repositories\Categories\Category;
use League\Fractal\ParamBag;
use League\Fractal\TransformerAbstract;

class CategoryTransformer extends TransformerAbstract
{
    use FilterTrait;
    protected $availableIncludes
        = [
            'details',
            'blogs',
        ];


    /**
     * Lấy thông tin của danh muc
     *
     *
     *
     *
     */
    public function transform(Category $category)
    {
        if (is_null($category)) {
            return [];
        }
        return [
            'id'         => $category->id,
            'hot'        => $category->hot ?? 0,
            'status'     => $category->status ?? 0,
            'new'        => $category->new ?? 0,
            'image'      => $category->image,
            'created_at' => $category->created_at,
            'updated_at' => $category->updated_at,
        ];
    }


    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param Category|null $category
     * @param ParamBag|null $params
     *
     * @return \League\Fractal\Resource\Collection|\League\Fractal\Resource\NullResource
     */
    public function includeDetails(Category $category = null, ParamBag $params = null)
    {
        if (is_null($category)) {
            return $this->null();
        }

        $data = $this->pagination($params, $category->categoryTrans());

        return $this->collection($data, new CategoryTranslateTransformer);
    }


    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param Category|null $category
     * @param ParamBag|null $params
     *
     * @return \League\Fractal\Resource\Collection|\League\Fractal\Resource\NullResource
     */
    public function includeBlogs(Category $category = null, ParamBag $params = null)
    {
        if (is_null($category)) {
            return $this->null();
        }

        $data = $this->pagination($params, $category->blogs());

        return $this->collection($data, new BlogTransformer);

    }


}
