<?php

namespace App\Repositories;


abstract class BaseLogic
{
    /**
     * @var BaseRepository
     */
    protected $model;

    /**
     * Lấy tất cả bản ghi
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @return Illuminate\Support\Collection
     */
    public function getAll() {
        return $this->model->getAll();
    }

    /**
     * Lấy tất cả bản ghi có phân trang
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param array $params
     * @param int   $size
     * @param int   $trash
     *
     * @return Illuminate\Pagination\Paginator
     */
    public function getByQuery($params = [], $size = 25, $trash = BaseRepository::NO_TRASH)
    {
        return $this->model->getByQuery($params, $size, $trash);
    }

    /**
     * Lấy thông tin 1 bản ghi đã bị xóa softDelete được xác định bởi ID
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $id
     *
     * @return Eloquent
     */
    public function getByIdInTrash($id)
    {
        return $this->model->getByIdInTrash($id);
    }

    /**
     * Lưu thông tin 1 bản ghi mới
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $data
     *
     * @return Eloquent
     */


    public function store($data)
    {
        return $this->model->store($data);
    }

    /**
     * Lưu thông tin nhiều bản ghi
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $data
     *
     * @return mixed
     */
    public function storeArray($data)
    {
        return $this->model->storeArray($data);
    }

    /**
     * Cập nhật thông tin 1 bản ghi theo ID
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param       $id
     * @param       $data
     * @param array $excepts
     * @param array $only
     *
     * @return Eloquent
     */
    public function update($id, $data, $excepts = [], $only = [])
    {
        return $this->model->update($id, $data, $excepts = [], $only = []);
    }

    /**
     * Lấy thông tin 1 bản ghi xác định bởi ID
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param      $id
     * @param bool $trash
     * @param bool $useHash
     *
     * @return Eloquent
     */
    public function getById($id, $trash = false, $useHash = false)
    {
        return $this->model->getById($id, $trash, $useHash);
    }

    /**
     * Xóa 1 bản ghi. Nếu model xác định 1 SoftDeletes
     * thì method này chỉ đưa bản ghi vào trash. Dùng method destroy
     * để xóa hoàn toàn bản ghi.
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $id
     *
     * @return bool|null
     */
    public function delete($id)
    {
        return $this->model->delete($id);
    }

    /**
     * Xóa hoàn toàn một bản ghi
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $id
     *
     * @return bool|null
     */
    public function destroy($id)
    {
        return $this->model->destroy($id);
    }

    /**
     * Khôi phục 1 bản ghi SoftDeletes đã xóa
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $id
     *
     * @return bool|null
     */
    public function restore($id)
    {
        return $this->model->restore($id);
    }
}