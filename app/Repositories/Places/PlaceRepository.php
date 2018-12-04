<?php

namespace App\Repositories\Places;

use App\Repositories\BaseRepository;

class PlaceRepository extends BaseRepository implements PlaceRepositoryInterface
{
    /**
     * Place model.
     * @var Model
     */
    protected $model;

    /**
     * PlaceRepository constructor.
     * @param Place $place
     */
    public function __construct(Place $place)
    {
        $this->model = $place;
    }

    /**
     * Lấy dữ liệu dựa theo longitude và latitude
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function getValuePlace($data)
    {
        return $this->model->where("longitude",$data['longitude'])->where('latitude',$data['latitude'])->first();
    }
}
