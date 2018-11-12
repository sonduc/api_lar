<?php

namespace App\Repositories\Promotions;

use App\Repositories\BaseRepository;

class PromotionRepository extends BaseRepository implements PromotionRepositoryInterface
{
    /**
     * Promotion model
     * @var Model
     */
    public function __construct(Promotion $promotion)
    {
        $this->model = $promotion;
    }


}
