<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 16/11/2018
 * Time: 13:19
 */

namespace App\Events;


class BookingConfirmEvent extends Event
{

    public $data;
    public $room;
    public $merchant;

    public function __construct($data,$merchant, $room)
    {
        $this->data         = $data;
        $this->merchant     = $merchant;
        $this->room         = $room;
    }

}
