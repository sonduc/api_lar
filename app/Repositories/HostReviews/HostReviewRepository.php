<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 1/28/2019
 * Time: 2:52 PM
 */

namespace App\Repositories\HostReviews;


use App\Repositories\BaseRepository;

class HostReviewRepository extends BaseRepository implements HostReviewRepositoryInterface
{
    /**
     * @var Setting
     */
    protected $model;

    /**
     * HostReviewRepository constructor.
     * @param HostReview $hostReview
     */
    public function __construct(
        HostReview $hostReview
    ) {
        $this->model         = $hostReview;
    }

}