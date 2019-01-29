<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 1/29/2019
 * Time: 3:51 AM
 */

namespace App\Events;


class Host_Reviews_Event extends Event
{
    public $data;

    public function __construct($data = [])
    {
        $this->data = $data;
    }

}