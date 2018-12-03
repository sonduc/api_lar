<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 03/12/2018
 * Time: 17:19
 */

namespace App\Events;


class Customer_Register_TypeBooking_Event extends Event
{
    public $data;


    public function __construct($data)
    {
        $this->data = $data;

    }


}
