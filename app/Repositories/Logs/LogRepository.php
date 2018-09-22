<?php

namespace App\Repositories\Logs;

use App\Repositories\BaseRepository;

class LogRepository extends BaseRepository
{
    /**
     * Log model.
     * @var Model
     */
    protected $model;
    
    /**
     * LogRepository constructor.
     *
     * @param Log $log
     */
    public function __construct(Log $log)
    {
        $this->model = $log;
    }
    
    /**
     * Lấy log
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param null $data
     * @param int  $pageSize
     *
     * @return \App\Repositories\Illuminate\Pagination\Paginator
     */
    public function getLog($data = null, $pageSize = 25)
    {
        if (array_key_exists('name', $data)) {
            return $this->getLogByName($data);
        }
        return parent::getByQuery($data, $pageSize);
    }
    
    /**
     * Lấy log dựa theo log_name
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param       $data
     * @param array $name
     *
     * @return mixed
     */
    public function getLogByName($data, $name = [])
    {
        $name = explode(',', $data['name']);
        
        return $this->model->whereIn('log_name', $name)->get();
    }
}
