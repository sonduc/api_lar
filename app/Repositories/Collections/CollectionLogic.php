<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 23/10/2018
 * Time: 17:02
 */
namespace App\Repositories\Collections;

use App\Repositories\BaseLogic;
use App\Events\AmazonS3_Upload_Event;

class CollectionLogic extends BaseLogic
{
    protected $model;
    protected $collectionTranslate;

    /**
     * CollectionLogic constructor.
     *
     * @param CollectionRepositoryInterface|CollectionRepository                  $collection
     * @param ColectionTranslateRepositoryInterface|CollectionTranslateRepository $collectionTranslate
     */
    public function __construct(
        CollectionRepositoryInterface $collection,
        ColectionTranslateRepositoryInterface $collectionTranslate
    ) {
        $this->model               = $collection;
        $this->collectionTranslate = $collectionTranslate;
    }

    /**
     * Thêm mới dữ liệu vào collection, collection_translate và collection_r
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param array $data
     *
     * @return \App\Repositories\Eloquent
     */
    public function store($data)
    {
        $name = rand_name($data['image']);
        event(new AmazonS3_Upload_Event($name, $data['image']));
        $data['image']   = $name;
        
        $data_collection = parent::store($data);
        $this->collectionTranslate->storeCollectionTranslate($data_collection, $data);
        $this->storeCollectionRoom($data_collection, $data);
        return $data_collection;
    }

    /**
     * Thêm mới dữ liệu vào collection_room
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $data_collection
     * @param $data
     */
    public function storeCollectionRoom($data_collection, $data)
    {
        if (!empty($data)) {
            if (isset($data['rooms'])) {
                $data_collection->rooms()->attach($data['rooms']);
            }
        }
    }

    /**
     * Cập nhật dữ liệu cho collection, collection_translate và collection_room
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param int   $id
     * @param       $data
     * @param array $excepts
     * @param array $only
     *
     * @return \App\Repositories\Eloquent
     */

    public function update($id, $data, $excepts = [], $only = [])
    {
        $collection = $this->model->getById($id);
        $name = rand_name($data['image']);
        event(new AmazonS3_Upload_Event($name, $data['image']));
        $data['image']   = $name;

        $data_collection = parent::update($id, $data);
        $this->collectionTranslate->updateCollectionTranslate($data_collection, $data);
        $this->updateCollectionRoom($data_collection, $data);
        return $data_collection;
    }

    /**
     * Cập nhật  dữ liệu vào collection_room
     * @author ducchien0612 <ducen0612@gmail.com>
     *
     * @param $data_collection
     * @param $data
     */
    public function updateCollectionRoom($data_collection, $data)
    {
        if (!empty($data)) {
            if (isset($data['rooms'])) {
                $data_collection->rooms()->detach();
                $data_collection->rooms()->attach($data['rooms']);
            }
        }
    }

    /**
     * Xóa bản ghi  collections và collection_translate
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $id
     */
    public function destroyColection($id)
    {
        $this->collectionTranslate->deleteCollectionTranslateByCollectionID($id);
        parent::destroy($id);
    }

    /**
     * Cập nhật một số trường trạng thái
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $id
     * @param $data
     *
     * @return \App\Repositories\Eloquent
     */
    public function singleUpdate($id, $data)
    {
        $data_collection = parent::update($id, $data);
        return $data_collection;
    }
}
