<?php

namespace App\Repositories\Comforts;

use App\Repositories\BaseRepository;

class ComfortRepository extends BaseRepository
{
    /**
     * Role model.
     * @var Model
     */
    protected $model;

    /**
     * ComfortRepository constructor.
     * @param Comfort $comfort
     */
    public function __construct(Comfort $comfort)
    {
        $this->model = $comfort;
    }
}
