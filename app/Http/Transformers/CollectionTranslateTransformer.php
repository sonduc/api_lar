<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 17/10/2018
 * Time: 13:57
 */

namespace App\Http\Transformers;

use App\Repositories\Collections\CollectionTranslate;
use League\Fractal\TransformerAbstract;
class CollectionTranslateTransformer extends TransformerAbstract
{
    protected $availableIncludes
        = [

        ];

    /**
     *
     * @param ComfortTranslate $comfort

     * @return array
     */
    public function transform(CollectionTranslate $collection)
    {
        if (is_null($collection)) {
            return [];
        }

        return [
            'id'                        => $collection->id,
            'colection_id'              => $collection->collection_id,
            'name'                      => $collection->name,
            'description'               => $collection->description,
            'lang'                      => $collection->lang,
            'created_at'                => $collection->created_at->format('Y-m-d H:i:s'),
            'updated_at'                => $collection->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
