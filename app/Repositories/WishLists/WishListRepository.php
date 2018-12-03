<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 03/12/2018
 * Time: 09:30
 */

namespace App\Repositories\WishLists;


use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Auth;

class WishListRepository  extends BaseRepository implements WishListRepositoryInterface
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
         WishList $wishList
    )
    {
        $this->model         = $wishList;
    }

}
