<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 17/10/2018
 * Time: 13:42
 */

namespace App\Repositories\Collections;

use App\Repositories\BaseRepository;
class CollectionTranslateRepository extends BaseRepository
{
    /**
     * Collection model.
     * @var Model
     */
    protected $model;

    /**
     * CollectionRepository constructor.
     * @param Collection $collection
     */
    public function __construct(CollectionTranslate $collection)
    {
        $this->model = $collection;
    }

    /**
     * Thêm dữ liệu vào collection_translate
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $collection
     * @param array $data
     * @param array $list
     */
    public function storeCollectionTranslate($collection, $data = [], $list = [])
    {
        if (!empty($data)) {
            if (isset($data['details'])) {
                foreach ($data['details']['data'] as $val) {
                    $val['collection_id']   = $collection->id;
                    $list[]                 = $val;
                }
            }
        }
        parent::storeArray($list);
    }

    /**
     * Cập nhật thông tin Collection theo ngôn ngữ
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $collection
     * @param array $data
     */
    public function updateCollectionTranslate($collection, $data = [])
    {
        $this->deleteCollectionTranslate($collection);
        $this->storeCollectionTranslate($collection, $data);
    }

    /**
     * Xóa tất cả bản ghi theo collection_id
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $room
     */
    public function deleteCollectionTranslate($collection)
    {
        $this->model->where('collection_id', $collection->id)->forceDelete();
    }


    /**
     * Xoa theo id truyen truc tiep
     * @author ducchien0612 <nxh0809@gmail.com>
     *
     * @param $id
     */
    public function deleteCollectionTranslateByCollectionID($id)
    {
        $this->model->where('collection_id', $id)->forceDelete();
    }

}
