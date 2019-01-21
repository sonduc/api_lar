<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 1/18/2019
 * Time: 3:35 PM
 */

namespace App\Repositories\SubTopic;


use App\Repositories\BaseRepository;

class SubTopicRepository extends BaseRepository implements SubTopicRepositoryInterface
{
    /**
     * @var Blog
     */
    protected $model;

    /**
     * BlogRepository constructor.
     *
     * @param Blog $blog
     */
    public function __construct(
        SubTopic $subTopic
    )
    {
        $this->model         = $subTopic;
    }

}