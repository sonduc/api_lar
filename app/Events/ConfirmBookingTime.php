<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 17/11/2018
 * Time: 09:42
 */

namespace App\Events;


class ConfirmBookingTime extends Event
{

    public $data;


    public function __construct($data)
    {
        $this->data = $data;

    }

}
