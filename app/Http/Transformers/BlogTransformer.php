<?php

namespace App\Http\Transformers;

use App\Repositories\Categories\Category;
use League\Fractal\TransformerAbstract;
use App\Repositories\Blogs\Blog;
use League\Fractal\ParamBag;
use App\Http\Transformers\Traits\FilterTrait;

class BlogTransformer extends TransformerAbstract
{
    use FilterTrait;
    protected $availableIncludes = [
        'details',
        'tags',
        'categories',
        'users'
    ];

    public function transform(Blog $blog = null)
    {
        if (is_null($blog)) {
            return [];
        }

        return [
            'id'                    => $blog->id,
            'image'                 => $blog->image,
            'status'                => $blog->status,
            'hot'                   => $blog->hot,
            'user_id'               => $blog->user_id,
            'category_id'           => $blog->category_id,
            'created_at'            => $blog->created_at->format('Y-m-d H:i:s'),
            'updated_at'            => $blog->updated_at->format('Y-m-d H:i:s'),


        ];
    }
    public function includeDetails(Blog $blog = null, ParamBag $params = null)
    {
        if (is_null($blog)) {
            return $this->null();
        }

        $data = $this->limitAndOrder($params, $blog->blogTrans())->get();

        return $this->collection($data,new BlogTranslateTransformer );
        //return $this->primitive($data);
    }

    /**
     *     Danh sách thẻ tags theo bài viết

     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param Blog|null $blog
     * @param ParamBag|null $params
     * @return \League\Fractal\Resource\Collection|\League\Fractal\Resource\NullResource
     */
    public function includeTags(Blog $blog = null, ParamBag $params = null)
    {
        if (is_null($blog)) {
            return $this->null();
        }
        $data = $this->limitAndOrder($params, $blog->tags())->get();
        return $this->collection($data, new TagTransformer);
    }

    /**
     * Xác định bài viết này thuộc danh mục nào
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param Blog|null $blog
     * @param ParamBag|null $params
     * @return \League\Fractal\Resource\Collection|\League\Fractal\Resource\NullResource
     */
    public function includeCategories (Blog $blog= null,ParamBag $params = null )
    {
        if (is_null($blog)) {
            return $this->null();
        }
        $data = $this->limitAndOrder($params, $blog->categories())->get();
        return $this->collection($data, new CategoryTransformer);

    }

    /**
     * Xác định xem ai viết blog này
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param Blog|null $blog
     * @param ParamBag|null $params
     * @return \League\Fractal\Resource\Collection|\League\Fractal\Resource\NullResource
     */
    public function includeUsers (Blog $blog= null,ParamBag $params = null )
    {
        if (is_null($blog)) {
            return $this->null();
        }
        $data = $this->limitAndOrder($params, $blog->users())->get();
        return $this->collection($data, new UserTransformer);
    }

}
