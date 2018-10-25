<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 17/10/2018
 * Time: 13:56
 */

namespace App\Http\Transformers;

use App\Http\Transformers\Traits\FilterTrait;
use App\Repositories\Collections\Collection;
use League\Fractal\ParamBag;
use League\Fractal\TransformerAbstract;

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
            'id'         => $collection->id,
            'image'      => $collection->image,
            'status'     => $collection->status ?? 0,
            'hot'        => $collection->hot ?? 0,
            'new'        => $collection->new ?? 0,
            'created_at' => $collection->created_at,
            'updated_at' => $collection->updated_at,
        ];
    }

    /**
     * Thông tin chi tiết bộ sưu tập
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param Collection|null $collection
     * @param ParamBag|null   $params
     *
     * @return \League\Fractal\Resource\Collection|\League\Fractal\Resource\NullResource
     */
    public function includeDetails(Collection $collection = null, ParamBag $params = null)
    {
        if (is_null($collection)) {
            return $this->null();
        }
        $data = $this->pagination($params, $collection->CollectionTrans());
        return $this->collection($data, new CollectionTranslateTransformer);
    }

    /**
     * Thông tin các phòng trong bộ sưu tập
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param Collection|null $collection
     * @param ParamBag|null   $params
     *
     * @return \League\Fractal\Resource\Collection|\League\Fractal\Resource\NullResource
     */

    public function includeRooms(Collection $collection = null, ParamBag $params = null)
    {
        if (is_null($collection)) {
            return $this->null();
        }
        $data = $this->pagination($params, $collection->rooms());
        return $this->collection($data, new RoomTransformer);
    }


}
