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
     * @param ComfortTranslate $comfort

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
            'content'                   => $blog->content,
            'slug'                      => $blog->slug,
            'lang'                      => $blog->lang,
            'created_at'                => $blog->created_at->format('Y-m-d H:i:s'),
            'updated_at'                => $blog->updated_at->format('Y-m-d H:i:s'),
        ];
    }


}
