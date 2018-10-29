<?php

namespace App\Http\Transformers;

use App\Http\Transformers\Traits\FilterTrait;
use App\Repositories\Comforts\Comfort;
use League\Fractal\ParamBag;
use League\Fractal\TransformerAbstract;

class ComfortTransformer extends TransformerAbstract
{
    use FilterTrait;
    protected $availableIncludes
        = [
            'details', 'rooms',
        ];


    /**
     * Lấy thông tin của tiện nghi
     *
     * @param Comfort $comfort
     *
     * @return array
     */
    public function transform(Comfort $comfort)
    {
        if (is_null($comfort)) {
            return [];
        }

        return [
            'id'         => $comfort->id,
            'icon'       => $comfort->icon,
            'created_at' => $comfort->created_at ? $comfort->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $comfort->created_at ? $comfort->updated_at->format('Y-m-d H:i:s') : null,
        ];
    }


    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param Comfort|null  $comfort
     * @param ParamBag|null $params
     *
     * @return \League\Fractal\Resource\Collection|\League\Fractal\Resource\NullResource
     */
    public function includeDetails(Comfort $comfort = null, ParamBag $params = null)
    {
        if (is_null($comfort)) {
            return $this->null();
        }

        $data = $this->pagination($params, $comfort->comfortTrans());

        return $this->collection($data, new ComfortTranslateTransformer);
    }

    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param Comfort|null  $comfort
     * @param ParamBag|null $params
     *
     * @return \League\Fractal\Resource\Collection|\League\Fractal\Resource\NullResource
     */
    public function includeRooms(Comfort $comfort = null, ParamBag $params = null)
    {
        if (is_null($comfort)) {
            return $this->null();
        }

        $data = $this->pagination($params, $comfort->rooms());

        return $this->collection($data, new RoomTransformer);
    }

}
