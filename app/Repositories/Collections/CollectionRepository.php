<?php

namespace App\Repositories\Collections;

use App\Repositories\BaseRepository;

class CollectionRepository extends BaseRepository implements CollectionRepositoryInterface
{
    /**
     * Collection model.
     * @var Model
     */
    public function __construct(Collection $collection)
    {
        $this->model = $collection;

    }

}
