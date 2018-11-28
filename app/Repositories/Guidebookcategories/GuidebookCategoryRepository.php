<?php

namespace App\Repositories\GuidebookCategories;

use App\Repositories\BaseRepository;

class GuidebookCategoryRepository extends BaseRepository implements GuidebookCategoryRepositoryInterface
{
    /**
     * GuidebookCategory model.
     * @var Model
     */
    protected $model;

    /**
     * GuidebookCategoryRepository constructor.
     * @param GuidebookCategory $guidebookcategory
     */
    public function __construct(GuidebookCategory $guidebookcategory)
    {
        $this->model = $guidebookcategory;
    }


}
