<?php

namespace App\Repositories\Categories;

use App\Repositories\BaseRepository;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    /**
     * CategoryPolicy model.
     * @var Model
     */
    public function __construct(
        Category $category
    )
    {
        $this->model = $category;
    }



}
