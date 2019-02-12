<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 1/18/2019
 * Time: 4:31 PM
 */

namespace App\Http\Transformers\Merchant;

use App\Http\Transformers\Traits\FilterTrait;
use App\Repositories\SubTopic\SubTopic;
use League\Fractal\TransformerAbstract;

class SubTopicTransformer extends TransformerAbstract
{
    use FilterTrait;
    protected $availableIncludes = [

    ];

    public function transform(SubTopic $subTopic)
    {
        if (is_null($subTopic)) {
            return [];
        }

        return [
            'id'                => $subTopic->id,
            'name'              => $subTopic->name,
            'topic_id'          => $subTopic->topic_id,
        ];
    }
}
