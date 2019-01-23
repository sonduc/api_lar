<?php

namespace App\Repositories\CompareCheckings;

use App\User;
use App\Repositories\BaseLogic;
use App\Repositories\CompareCheckings\CompareCheckingRepositoryInterface;
use Carbon\Carbon;

class CompareCheckingLogic extends BaseLogic
{
    public function __construct(
        CompareCheckingRepositoryInterface $compare
    ) {
        $this->model  = $compare;
    }

    /**
     *
     * @author Tuan Anh <tuananhpham1402@gmail.com>
     *
     * @param $id
     * @param array $data
     * @return \App\Repositories\Eloquent
     * @throws \Exception
     */
    public function minorCompareCheckingUpdate($id, $data = [])
    {
        return parent::update($id, $data);
    }
}
