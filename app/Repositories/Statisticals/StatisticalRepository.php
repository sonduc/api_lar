<?php

namespace App\Repositories\Statisticals;

use App\Repositories\BaseRepository;

class StatisticalRepository extends BaseRepository implements StatisticalRepositoryInterface
{
    /**
     * Statistical model.
     * @var Model
     */
    protected $model;

    /**
     * StatisticalRepository constructor.
     * @param Statistical $statistical
     */
    public function __construct(Statistical $statistical)
    {
        $this->model = $statistical;
    }


}
