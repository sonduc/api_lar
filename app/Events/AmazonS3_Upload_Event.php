<?php

namespace App\Events;

class AmazonS3_Upload_Event extends Event
{
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $data;
    public $name;

    public function __construct($name, $data = [])
    {
        $this->name = $name;
        $this->data = $data;
    }
}
