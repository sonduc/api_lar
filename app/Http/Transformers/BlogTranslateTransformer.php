<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 28/09/2018
 * Time: 13:09
 */

namespace App\Http\Transformers;

use App\Repositories\Blogs\BlogTranslate;
use League\Fractal\TransformerAbstract;
class BlogTranslateTransformer extends  TransformerAbstract
{
    protected $availableIncludes
        = [

        ];

    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param BlogTranslate $blog
     *
     * @return array
     */
    public function transform(BlogTranslate $blog)
    {
        if (is_null($blog)) {
            return [];
        }

        return [
            'id'                        => $blog->id,
            'blog_id'                   => $blog->blog_id,
            'title'                     => $blog->title,
            'content'                   => $blog->content,
            'slug'                      => $blog->slug,
            'lang'                      => $blog->lang,
            'created_at'                => $blog->created_at ? $blog->created_at->format('Y-m-d H:i:s') : null,
            'updated_at'                => $blog->updated_at ? $blog->updated_at->format('Y-m-d H:i:s') : null,
        ];
    }


}
