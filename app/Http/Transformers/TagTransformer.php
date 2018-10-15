<?php

namespace App\Http\Transformers;

use League\Fractal\TransformerAbstract;
use App\Repositories\Blogs\Tag;

class TagTransformer extends TransformerAbstract
{
    protected $availableIncludes = [

    ];

    public function transform(Tag $tag = null)
    {
        if (is_null($tag)) {
            return [];
        }

        return [
            'id'            => $tag->id,
            'name'          => $tag->name,
            'slug'          => $tag->slug,

        ];
    }

}
