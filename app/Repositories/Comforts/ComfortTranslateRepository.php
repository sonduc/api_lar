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
    public function storeComfortTranslate($data, $id)
    {
        $data['comfort_id'] = $id;
        parent::store($data);
    }

    public function updateComfortTranslate($data, $id)
    {

        $data['comfort_id'] = $id;

        $count = $this->model->where([
            ['comfort_id', $id],
            ['lang_id', $data['lang_id']]
        ])->first();
        $count ? parent::update($id, $data) : parent::store($data);
    }

    public function getByComfortID($id)
    {
        return $this->model->where('comfort_id', $id)->select('id')->get();
    }

}
