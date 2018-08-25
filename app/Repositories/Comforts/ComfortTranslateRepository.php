<?php

namespace App\Repositories\Comforts;

use App\Repositories\BaseRepository;

class ComfortTranslateRepository extends BaseRepository
{
    /**
     * Model.
     * @var Model
     */
    protected $model;

    /**
     * ComfortRepository constructor.
     * @param Comfort $comfort
     */
    public function __construct(ComfortTranslate $comfort)
    {
        $this->model = $comfort;
    }

    public function getByComfortID($id)
    {
        return $this->model->where('comfort_id', $id)->select('id')->get();
    }
}
