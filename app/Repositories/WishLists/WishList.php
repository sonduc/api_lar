<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 03/12/2018
 * Time: 09:46
 */

namespace App\Repositories\WishLists;


use App\Repositories\Entity;
use App\Repositories\Rooms\Room;
use Illuminate\Database\Eloquent\SoftDeletes;

class WishList extends Entity
{
    use SoftDeletes;

    protected $table = 'wish_lists';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable
        = [
            'room_id', 'customer_id'

        ];

    /**
     * The attributes that are cast permission from json string to array
     * @var array
     */
    protected $casts = ['permissions' => 'array'];


    public function rooms()
    {
        return $this->hasMany(Room::class, 'id','room_id');
    }

}
