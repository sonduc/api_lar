<?php

namespace App\Repositories\Blogs;

use App\Repositories\BaseRepository;

class BlogRepository extends BaseRepository implements BlogRepositoryInterface
{
    /**
     * @var Blog
     */
    protected $model;

    public function __construct(
        Blog $blog
    )
    {
        $this->model         = $blog;
    }

}
