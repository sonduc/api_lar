<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 1/17/2019
 * Time: 3:43 AM
 */

namespace App\Http\Transformers;

use App\Repositories\Seo\Seo;
use App\Http\Transformers\Traits\FilterTrait;
use League\Fractal\TransformerAbstract;
class SeoTransformers extends TransformerAbstract
{
    use FilterTrait;
    protected $availableIncludes = [

    ];

    public function transform(Seo $seo= null)
    {
        if (is_null($seo)) {
            return [];
        }

        return [
            'id'                => $seo->id,
            'meta_titlte'       => $seo->meta_title,
            'meta_description'  => $seo->meta_description,
            'meta_keywords'     => json_decode($seo->meta_keywords),
            'created_at'        => $seo->created_at ? $seo->created_at->format('Y-m-d H:i:s') : null,
            'updated_at'        => $seo->updated_at ? $seo->updated_at->format('Y-m-d H:i:s') : null
        ];
    }

}