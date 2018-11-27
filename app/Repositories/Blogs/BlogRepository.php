<?php

namespace App\Repositories\Blogs;

use App\Repositories\BaseRepository;

class BlogRepository extends BaseRepository implements BlogRepositoryInterface
{
    /**
     * @var Blog
     */
    protected $model;
    protected $blogTranslate;
    protected $tag;


    /**
     * BlogRepository constructor.
     *
     * @param Blog $blog
     */
    public function __construct(
        Blog $blog
    ) {
        $this->model         = $blog;
    }
}
