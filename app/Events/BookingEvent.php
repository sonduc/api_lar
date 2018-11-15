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
    public $room;
    public $merchant;

    public function __construct($data,$merchant, $room)
    {
        $this->data         = $data;
        $this->merchant     = $merchant;
        $this->room    = $room;
    }
}
