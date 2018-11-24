<?php
namespace App\Events;


class Booking_Reviews_Event extends Event
{
    public $data;
    public $name;

    public function __construct($name, $data = [])
    {
        $this->name = $name;
        $this->data = $data;
    }

}
