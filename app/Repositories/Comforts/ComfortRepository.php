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
    protected  $comfortTranslate;


    /**
     * ComfortRepository constructor.
     * @param Comfort $comfort
     * @param ComfortTranslateRepository $comfortTranslate
     */
    public function __construct(Comfort $comfort, ComfortTranslateRepository $comfortTranslate)
    {
        $this->model = $comfort;
        $this->comfortTranslate = $comfortTranslate;
    }

    /**
     * Thêm mới một bản ghi vào comforts và comforts_translate
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param array $data
     * @return \App\Repositories\Eloquent
     */
    public function store($data)
    {
        $data_comfort = parent::store($data);
        $this->comfortTranslate->storeComfortTranslate($data, $data_comfort->id);
        return $data_comfort;
    }

    /**
     * câp nhật thông tin bản ghi vào comforts và  comforts_translate
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param int $id
     * @param $data
     * @param array $except
     * @param array $only
     * @return bool
     */
    public function update($id, $data, $except = [], $only = [])
    {
        $data_comfort    = parent::update($id, $data);
        $this->comfortTranslate->updateComfortTranslate($data, $data_comfort->id);
        return $data_comfort;
    }

    /**
     * Xóa một bản ghi ở bảng comforts và comforts_translate
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $id
     */
    public function deleteRoom($id)
    {
        $list_id = $this->comfortTranslate->getByComfortID($id);
        foreach ($list_id as $comfort) {
            $this->comfortTranslate->delete($comfort->id);
        }
        parent::delete($id);
    }

}
