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
        return parent::getByQuery($data, $pageSize);
    }
}
