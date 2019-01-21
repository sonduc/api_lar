<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 1/18/2019
 * Time: 3:36 PM
 */

namespace App\Repositories\Topic;


use App\Repositories\BaseRepository;

class TopicRepository extends BaseRepository implements TopicRepositoryInterface
{
    /**
     * @var Topic
     */
    protected $model;

    /**
     * TopicRepository constructor.
     * @param Topic $topic
     */
    public function __construct(
        Topic $topic
    )
    {
        $this->model         = $topic;
    }

}