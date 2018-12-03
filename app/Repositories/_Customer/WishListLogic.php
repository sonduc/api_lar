<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 03/12/2018
 * Time: 10:08
 */

namespace App\Repositories\_Customer;


use App\Repositories\BaseLogic;
use App\Repositories\Rooms\RoomRepositoryInterface;
use App\Repositories\WishLists\WishListRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class WishListLogic extends BaseLogic
{
    protected $room;


    public function __construct(
        WishListRepositoryInterface $model,
        RoomRepositoryInterface $room
    ) {
        $this->model          = $model;
        $this->room           = $room;
    }

    /**
     *
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $data
     * @return \App\Repositories\Eloquent
     */
    public function store($data)
    {
        $data['customer_id'] = Auth::user()->id;
        return parent::store($data);
    }


    /**
     *
     * @author ducchien0612 <ducchien0612@gmail.com>
     *
     * @param $id
     * @param null $data
     * @param array $except
     * @param array $only
     * @return \App\Repositories\Eloquent
     */
    public function update($id, $data = null, $except = [], $only = [])
    {
        return parent::update($id, $data);

    }

}
