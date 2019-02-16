<?php

namespace App\Http\Transformers\Customer;

use League\Fractal\TransformerAbstract;
use App\Repositories\Blogs\Tag;

class TagTransformer extends TransformerAbstract
{
    protected $availableIncludes = [

    ];

    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param Tag|null $tag
     *
     * @return array
     */
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
