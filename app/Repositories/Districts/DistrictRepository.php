<?php

namespace App\Repositories\Districts;

use App\Repositories\BaseRepository;

class DistrictRepository extends BaseRepository
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
    public function __construct(District $district)
    {
        $this->model = $district;
        
    }
    
    
}
