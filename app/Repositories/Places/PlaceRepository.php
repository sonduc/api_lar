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


}
