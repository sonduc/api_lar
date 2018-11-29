<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 28/11/2018
 * Time: 15:31
 */

namespace App\Events;


class Reset_Password_Event extends Event
{
    public $data;


    public function __construct($data)
    {
        $this->data = $data;
    }

}
