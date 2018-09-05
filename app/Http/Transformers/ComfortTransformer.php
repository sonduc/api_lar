<?php

namespace App\Http\Transformers;

use App\Repositories\Comforts\Comfort;
use League\Fractal\TransformerAbstract;

class ComfortTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'details','rooms'
    ];


    /**
     * Lấy thông tin của tiện nghi
     * @param Comfort $comfort
     * @return array
     */
    public function transform(Comfort $comfort)
    {
        if (is_null($comfort)) {
            return [];
        }

        return [
            'id'                    => $comfort->id,
            'icon'                  => $comfort->icon,
            'created_at'            => $comfort->created_at->format('Y-m-d H:i:s'),
            'updated_at'            => $comfort->updated_at->format('Y-m-d H:i:s'),
        ];
    }


    /**
     * Thông tin chi tiết tiện nghi
     * @param Comfort|null $comfort
     * @return $comfort->comfortTrans
     */
    public function includeDetails(Comfort $comfort = null)
    {
        if (is_null($comfort)) {
            return $this->null();
        }
        return $this->collection($comfort->comfortTrans, new ComfortTranslateTransformer);
    }

    public function includeRooms(Comfort $comfort = null)
    {
        if (is_null($comfort)) {
            return $this->null();
        }
        return $this->collection($comfort->rooms, new RoomTransformer);
    }

}
