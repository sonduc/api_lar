<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 17/10/2018
 * Time: 13:56
 */

namespace App\Http\Transformers;

use App\Repositories\Collections\Collection;
use League\Fractal\TransformerAbstract;
use League\Fractal\ParamBag;
use App\Http\Transformers\Traits\FilterTrait;
class CollectionTransformer extends TransformerAbstract
{
    use FilterTrait;
    protected $availableIncludes = [
        'details',
        'rooms',
    ];

    public function transform(Collection $collection = null)
    {
        if (is_null($collection)) {
            return [];
        }

        return [
            'id'                    => $collection->id,
            'image'                 => $collection->image,
            'status'                => $collection->status,
            'hot'                   => $collection->hot,
            'new'                   => $collection->new,
            'created_at'            => $collection->created_at->format('Y-m-d H:i:s'),
            'updated_at'            => $collection->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Thông tin chi tiết bộ sưu tập
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param Collection|null $collection
     * @param ParamBag|null $params
     * @return \League\Fractal\Resource\Collection|\League\Fractal\Resource\NullResource
     */
    public function includeDetails(Collection $collection = null, ParamBag $params = null)
    {
        if (is_null($collection)) {
            return $this->null();
        }
        $data = $this->limitAndOrder($params, $collection->collectionTrans())->get();
        return $this->collection($data,new CollectionTranslateTransformer);
        //return $this->primitive($data);
    }

    /**
     * Thông tin các phòng trong bộ sưu tập
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param Collection|null $collection
     * @param ParamBag|null $params
     * @return \League\Fractal\Resource\Collection|\League\Fractal\Resource\NullResource
     */

    public function includeRooms(Collection $collection = null, ParamBag $params = null)
    {
        if (is_null($collection)) {
            return $this->null();
        }
        $data = $this->limitAndOrder($params, $collection->rooms())->get();
        return $this->collection($data,new RoomTransformer);
        //return $this->primitive($data);
    }


}
