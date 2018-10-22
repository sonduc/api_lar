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
            'id'                => $category->id,
            'hot'               => $category->hot,
            'status'            => $category->status,
            'status'            => $category->new,
            'image'             => $category->image,
            'created_at'        => $category->created_at->format('Y-m-d H:i:s'),
            'updated_at'        => $category->updated_at->format('Y-m-d H:i:s'),
        ];
    }


    /**
     * Thông tin chi tiết danh muc
     *
     * @param Category|null $category
     *
     *
     */
    public function includeDetails(Category $category = null, ParamBag $params = null)
    {
        if (is_null($category)) {
            return $this->null();
        }

        $data = $this->limitAndOrder($params, $category->categoryTrans())->get();

        return $this->collection($data,new CategoryTranslateTransformer );
      //return $this->primitive($data);
    }


    /**
     * Thông tin chi tiết bài viết theo danh mục.
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function includeBlogs(Category $category = null, ParamBag $params = null)
    {
        if (is_null($category)) {
            return $this->null();
        }

        $data = $this->limitAndOrder($params, $category->blogs())->get();

        return $this->collection($data,new BlogTransformer );
        //return $this->primitive($data);

    }


    }
