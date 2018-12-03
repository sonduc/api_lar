<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 03/12/2018
 * Time: 10:52
 */

namespace App\Http\Transformers\Customer;

use App\Http\Transformers\Traits\FilterTrait;
use App\Repositories\WishLists\WishList;
use League\Fractal\ParamBag;
use League\Fractal\TransformerAbstract;
class WishListTransformer extends TransformerAbstract
{
    use FilterTrait;
    protected $availableIncludes = [
        'rooms'
    ];

    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param Booking|null $booking
     *
     * @return array
     */
    public function transform(WishList $wish_list = null)
    {
        if (is_null($wish_list)) {
            return [];
        }

        return [
            'id'                 => $wish_list->id,
            'room_id'            => $wish_list->room_id,
        ];
    }


    public function includeRooms(WishList $wish_list = null, ParamBag $params = null)
    {
        if (is_null($wish_list)) {
            return $this->null();
        }

        $data = $this->pagination($params, $wish_list->rooms());


        return $this->collection($data, new RoomTransformer);
        //return $this->primitive($data);
    }
}
