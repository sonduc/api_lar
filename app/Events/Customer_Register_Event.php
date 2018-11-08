<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 06/11/2018
 * Time: 11:49
 */

namespace App\Events;


class Customer_Register_Event extends Event
{
    public $data;
    public $name;

    public function __construct($name, $data = [])
    {
        $this->name = $name;
        $this->data = $data;
    }

}
