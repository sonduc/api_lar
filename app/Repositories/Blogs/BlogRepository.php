<?php

namespace App\Repositories\Blogs;

use App\Repositories\BaseRepository;

class BlogRepository extends BaseRepository
{
    /**
     * Blog model.
     * @var Model
     */
    protected $model;

    /**
     * BlogRepository constructor.
     *
     * @param Blog $blog
     */
    public function __construct(Blog $blog)
    {
        $this->model = $blog;
    }

    public function store($data = null)
    {
        return parent::store($data);
    }

    public function update($id, $data = null, $except = [], $only = [])
    {
        return parent::update($id, $data);
    }

}
