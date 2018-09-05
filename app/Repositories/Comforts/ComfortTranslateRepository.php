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
    public function storeComfortTranslate($comfort, $data = [], $list = [])
    {
        if (!empty($data)) {
            if (isset($data['details']['data'])) {
                foreach ($data['details']['data'] as $val) {
                    $val['comfort_id']          = $comfort->id;
                    $list[]                     = $val;
                }
            }
        }

        parent::storeArray($list);
    }

    public function updateComfortTranslate($comfort, $data = [])
    {
        $this->deleteComfortTranslateByRoomID($comfort);
        $this->storeComfortTranslate($comfort, $data);

//        $data['comfort_id'] = $id;
//
//        $count = $this->model->where([
//            ['comfort_id', $id],
//            ['lang_id', $data['lang_id']]
//        ])->first();
//        $count ? parent::update($id, $data) : parent::store($data);
    }

    public function getByComfortID($id)
    {
        return $this->model->where('comfort_id', $id)->select('id')->get();
    }
    public function deleteComfortTranslateByRoomID($comfort)
    {
        $this->model->where('comfort_id', $comfort->id)->forceDelete();
    }

}
