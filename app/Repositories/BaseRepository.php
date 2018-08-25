<?php
namespace App\Repositories;

abstract class BaseRepository implements EntityInterface
{

    const WITH_TRASH    = 1;
    const ONLY_TRASH    = 2;
    const NO_TRASH      = 0;

    /**
     * Eloquent model
     * @var Eloquent
     */
    protected $model;

    /**
     * Lấy tất cả bản ghi của model
     * @author SaturnLai <daolvcntt@gmail.com>
     *
     * @return Illuminate\Support\Collection
     */
    public function getAll()
    {
        return $this->model->all();
    }

    /**
     * Lấy tất cả bản ghi có phân trang
     * @author SaturnLai <daolvcntt@gmail.com>
     *
     * @param  integer $size Số bản ghi mặc định 25
     * @param  array $sorting Sắp xếp
     * @return Illuminate\Pagination\Paginator
     */
    public function getByQuery($params = [], $size = 25, $trash = self::NO_TRASH)
    {
        $sort = array_get($params, 'sort', 'created_at:-1');
        $params['sort'] = $sort;
        $lModel = $this->model;
        $params = array_except($params, ['page', 'limit']);
//        dd($params);
        if (count($params)) {
            $reflection = new \ReflectionClass($lModel);
            foreach ($params as $funcName => $funcParams) {
                $funcName = \Illuminate\Support\Str::studly($funcName);
                if ($reflection->hasMethod('scope' . $funcName)) {
                    $funcName = lcfirst($funcName);
                    $lModel = $lModel->$funcName($funcParams);
                }
            }
        }

        switch ($trash) {
            case self::WITH_TRASH:
                $lModel->withTrashed();
                break;
            case self::ONLY_TRASH:
                $lModel->onlyTrashed();
                break;
            case self::NO_TRASH:
            default:
                break;
        }

        switch ($size) {
            case -1:
                return $lModel->get();
                break;
            case 0:
                return $lModel->first();
            default:
                return $lModel->paginate($size);
                break;
        }
    }

    /**
     * Lấy thông tin 1 bản ghi xác định bởi ID
     * @author SaturnLai <daolvcntt@gmail.com>
     *
     * @param  integer $id ID bản ghi
     * @param  boolean $trash Có lấy softDeletes
     * @param  boolean $useHash Có sử dụng hash hay không
     * @return Eloquent
     */
    public function getById($id, $trash = false, $useHash = true)
    {
        $model = $this->model;
        if ($trash) {
            $model = $model->withTrashed();
        }
        if ($useHash && !is_numeric($id)) {
            return $model->findOrFail(hashid_decode($id));
        }
        return $model->findOrFail($id);
    }

    /**
     * Lấy thông tin 1 bản ghi đã bị xóa softDelete được xác định bởi ID
     * @author SaturnLai <daolvcntt@gmail.com>
     *
     * @param  integer $id ID bản ghi
     * @return Eloquent
     */
    public function getByIdInTrash($id)
    {
        return $this->model->withTrashed()->findOrFail($id);
    }

    /**
     * Lưu thông tin 1 bản ghi mới
     * @author SaturnLai <daolvcntt@gmail.com>
     *
     * @param  array $data
     * @return Eloquent
     */
    public function store($data)
    {
        return $this->model->create($data);
    }
    /**
     * Lưu thông tin nhiều bản ghi
     * @author SaturnLai <daolvcntt@gmail.com>
     * @param  [type]     $datas [description]
     * @return [type]            [description]
     */
    public function storeArray($datas)
    {
        return $this->model->insert($datas);
    }

    /**
     * Cập nhật thông tin 1 bản ghi theo ID
     * @author SaturnLai <daolvcntt@gmail.com>
     *
     * @param  integer $id ID bản ghi
     * @return bool
     */
    public function update($id, $data, $excepts = [], $only = [])
    {
        $data = array_except($data, $excepts);
        if (count($only)) {
            $data = array_only($data, $only);
        }
        $record = $this->getById($id);
        $record->fill($data)->save();
        return $record;
    }

    /**
     * Xóa 1 bản ghi. Nếu model xác định 1 SoftDeletes
     * thì method này chỉ đưa bản ghi vào trash. Dùng method destroy
     * để xóa hoàn toàn bản ghi.
     * @author SaturnLai <daolvcntt@gmail.com>
     *
     * @param  integer $id ID bản ghi
     * @return bool|null
     */
    public function delete($id)
    {
        $record = $this->getById($id);
        return $record->delete();
    }

    /**
     * Xóa hoàn toàn một bản ghi
     * @author SaturnLai <daolvcntt@gmail.com>
     * @param  integer $id ID bản ghi
     * @return bool|null
     */
    public function destroy($id)
    {
        $record = $this->getById($id);
        return $record->forceDelete();
    }

    /**
     * Khôi phục 1 bản ghi SoftDeletes đã xóa
     * @author SaturnLai <daolvcntt@gmail.com>
     * @param  integer $id ID bản ghi
     * @return bool|null
     */
    public function restore($id)
    {
        $record = $this->getById($id);
        return $record->restore();
    }
}
