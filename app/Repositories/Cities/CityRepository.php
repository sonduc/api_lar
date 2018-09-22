<?php

namespace App\Repositories\Cities;

use App\Repositories\BaseRepository;

class CityRepository extends BaseRepository
{
    /**
     * Role model.
     * @var Model
     */
    protected $model;
    
    /**
     * CityRepository constructor.
     *
     * @param City $city
     */
    public function __construct(City $city)
    {
        $this->model = $city;
    }
    
    
}
