<?php

namespace App\Events;

class CreateBookingTransactionEvent extends Event
{
    public $data;

    public function __construct($data = [])
    {
        $this->data = $data;
    }
}
