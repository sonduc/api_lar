<?php

namespace App\Events;

class MerchantRegisterReferralTransactionEvent extends Event
{
    public $data;

    public function __construct($data = [])
    {
        $this->data = $data;
    }
}
