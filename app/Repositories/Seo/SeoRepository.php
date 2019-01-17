<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 1/16/2019
 * Time: 5:28 PM
 */

namespace App\Repositories\Seo;


use App\Repositories\BaseRepository;

class SeoRepository extends BaseRepository implements SeoRepositoryInterface
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
        Seo $seo
    ) {
        $this->model         = $seo;
    }

}