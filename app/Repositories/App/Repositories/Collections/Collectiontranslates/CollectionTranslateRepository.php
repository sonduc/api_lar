<?php

namespace App\Repositories\App\Repositories\Collections\CollectionTranslates;

use App\Repositories\BaseRepository;

class CollectiontranslateRepository extends BaseRepository
{
    /**
     * Collectiontranslate model.
     * @var Model
     */
    protected $model;

    /**
     * CollectiontranslateRepository constructor.
     * @param Collectiontranslate $collectiontranslate
     */
    public function __construct(Collectiontranslate $collectiontranslate)
    {
        $this->model = $collectiontranslate;
    }


}
