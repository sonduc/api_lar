<?php

namespace App\Repositories\_Customer;

use App\Repositories\BaseLogic;
use App\Repositories\Promotions\PromotionRepositoryInterface;

class PromotionLogic extends BaseLogic
{
    protected $model;

    public function __construct(PromotionRepositoryInterface $promotion)
    {
        $this->model = $promotion;
    }
}
