<?php

namespace App\Events;

class BookingEvent extends Event
{
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $data;
    public $room_name;
    public $merchant;

    public function __construct($data,$merchant, $room_name)
    {
        $this->data         = $data;
        $this->merchant     = $merchant;
        $this->room_name    = $room_name;
    }
}
