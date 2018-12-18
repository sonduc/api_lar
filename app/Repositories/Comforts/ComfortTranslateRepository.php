<?php

namespace App\Repositories\Comforts;

use App\Repositories\BaseRepository;

class ComfortTranslateRepository extends BaseRepository implements ComfortTranslateRepositoryInterface
{
    /**
     * Model.
     * @var Model
     */
    protected $model;

    /**
     * ComfortRepository constructor.
     *
     * @param Comfort $comfort
     */
    public function __construct(ComfortTranslate $comfort)
    {
        $this->model = $comfort;
    }

    public function updateComfortTranslate($comfort, $data = [])
    {
        $this->deleteComfortTranslateByComfortID($comfort);
        $this->storeComfortTranslate($comfort, $data);

    }

    public function deleteComfortTranslateByComfortID($comfort)
    {
        $this->model->where('comfort_id', $comfort->id)->forceDelete();
    }

    public function storeComfortTranslate($comfort, $data = [], $list = [])
    {
        if (!empty($data)) {
            if (isset($data['details'])) {
                foreach ($data['details'] as $val) {
                    $val['comfort_id'] = $comfort->id;
                    $list[]            = $val;
                }
            }
        }

        parent::storeArray($list);
    }

    public function getByComfortID($id)
    {
        return $this->model->where('comfort_id', $id)->select('id')->get();
    }
}
