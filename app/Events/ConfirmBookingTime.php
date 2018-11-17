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
    public $uuid;


    public function __construct($data,$uuid)
    {
        $this->data         = $data;
        $this->uuid         = $uuid;
    }

}
