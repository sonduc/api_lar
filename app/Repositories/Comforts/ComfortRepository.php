<?php

namespace App\Repositories\Comforts;

use App\Repositories\BaseRepository;

class ComfortRepository extends BaseRepository implements ComfortRepositoryInterface
{
    /**
     * Role model.
     * @var Model
     */
    protected $model;
    protected $comfortTranslate;


    /**
     * ComfortRepository constructor.
     *
     * @param Comfort                    $comfort
     * @param ComfortTranslateRepository $comfortTranslate
     */
    public function __construct(Comfort $comfort, ComfortTranslateRepositoryInterface $comfortTranslate)
    {
        $this->model            = $comfort;
        $this->comfortTranslate = $comfortTranslate;
    }

    /**
     * Thêm mới  bản ghi vào comforts và comforts_translate
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param array $data
     *
     * @return \App\Repositories\Eloquent
     */
    public function store($data)
    {
        $data_comfort = parent::store($data);
        $this->comfortTranslate->storeComfortTranslate($data_comfort, $data);
        return $data_comfort;
    }

    /**
     * câp nhật thông tin bản ghi vào comforts và  comforts_translate
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param int   $id
     * @param       $data
     * @param array $except
     * @param array $only
     *
     * @return bool
     */
    public function update($id, $data, $except = [], $only = [])
    {
        $data_catagory = parent::update($id, $data);
        $this->comfortTranslate->updateComfortTranslate($data_catagory, $data);
        return $data_catagory;
    }

    /**
     * Xóa bản ghi ở bảng comforts và comforts_translate
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $id
     */
    public function deleteComfort($id)
    {
        $list_id = $this->comfortTranslate->getByComfortID($id);
        foreach ($list_id as $comfort) {
            $this->comfortTranslate->delete($comfort->id);
        }
        parent::delete($id);
    }
}
