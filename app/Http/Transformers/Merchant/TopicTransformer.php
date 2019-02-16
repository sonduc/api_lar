<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 1/18/2019
 * Time: 4:30 PM
 */

namespace App\Http\Transformers\Merchant;

use App\Http\Transformers\Traits\FilterTrait;
use App\Repositories\Topic\Topic;
use League\Fractal\TransformerAbstract;
use App\Http\Transformers\Merchant\SubTopicTransformer;

class TopicTransformer extends TransformerAbstract
{
    use FilterTrait;
    protected $availableIncludes = [
        'sub'
    ];

    public function transform(Topic $topic = null)
    {
        if (is_null($topic)) {
            return [];
        }

        return [
            'id'                => $topic->id,
            'name'              => $topic->name,
        ];
    }
    
    /**
    *
    * @author HarikiRito <nxh0809@gmail.com>
    *
    * @param Topic|null $topic
    *
    * @return \League\Fractal\Resource\Item|\League\Fractal\Resource\NullResource
    */
    public function includeSub(Topic $topic = null)
    {
        if (is_null($topic)) {
            return $this->null();
        }

        return $this->collection($topic->subs, new SubTopicTransformer);
    }
}
