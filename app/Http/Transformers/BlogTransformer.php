<?php

namespace App\Http\Transformers;

use App\Repositories\Blogs\Blog;
use League\Fractal\TransformerAbstract;

class BlogTransformer extends TransformerAbstract
{
    protected $availableIncludes
        = [

        ];

    public function transform(Blog $blog = null)
    {
        if (is_null($blog)) {
            return [];
        }

        return [
            'id' => $blog->id,
        ];
    }

}
