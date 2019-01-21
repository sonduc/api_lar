<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 1/18/2019
 * Time: 4:30 PM
 */

namespace App\Http\Transformers;

use App\Http\Transformers\Traits\FilterTrait;
use App\Repositories\Topic\Topic;
use League\Fractal\TransformerAbstract;
class TopicTransformer extends TransformerAbstract
{
    use FilterTrait;
    protected $availableIncludes = [

    ];

    public function transform(Topic $topic= null)
    {
        if (is_null($topic)) {
            return [];
        }

        return [
            'id'                => $topic->id,
            'name'              => $topic->name,
        ];
    }

}