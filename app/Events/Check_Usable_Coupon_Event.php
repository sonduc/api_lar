<?php

namespace App\Events;


class Check_Usable_Coupon_Event extends Event
{
    public $data;
    public $name;

    public function __construct($name, $data = [])
    {
        $this->name = $name;
        $this->data = $data;
    }

}
