<?php

namespace App\Http\Transformers;

use App\Http\Transformers\Traits\FilterTrait;
use App\Repositories\Districts\District;
use League\Fractal\ParamBag;
use League\Fractal\TransformerAbstract;

class DistrictTransformer extends TransformerAbstract
{
    use FilterTrait;
    protected $availableIncludes
        = [
            'rooms',
            'users',
        ];

    public function transform(District $district = null)
    {
        if (is_null($district)) {
            return [];
        }

        return [
            'id'           => $district->id,
            'name'         => $district->name,
            'short_name'   => $district->short_name,
            'code'         => $district->code,
            'priority'     => $district->priority,
            'priority_txt' => $district->getPriorityStatus(),
            'hot'          => $district->hot,
            'city_id'      => $district->city_id,
            'status'       => $district->status,
            'status_txt'   => $district->getStatus(),
            'updated_at'   => $district->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Include Rooms
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param District|null $district
     *
     * @return \League\Fractal\Resource\Collection|\League\Fractal\Resource\NullResource
     */
    public function includeRooms(District $district = null, ParamBag $params = null)
    {
        if (is_null($district)) {
            return $this->null();
        }

        $data = $this->limitAndOrder($params, $district->rooms())->get();
        return $this->collection($data, new RoomTransformer);
    }

    /**
     * Include Users
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param District|null $district
     * @param ParamBag|null $params
     *
     * @return \League\Fractal\Resource\Collection|\League\Fractal\Resource\NullResource
     */
    public function includeUsers(District $district = null, ParamBag $params = null)
    {
        if (is_null($district)) {
            return $this->null();
        }

        $data = $this->limitAndOrder($params, $district->users())->get();
        return $this->collection($data, new UserTransformer);
    }

}
