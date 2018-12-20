<?php

namespace App\Events;

class CreateCouponUserRegister extends Event
{
    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }
}
