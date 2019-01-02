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
}
