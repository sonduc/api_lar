<?php

namespace App\Jobs;

use App\Services\Email\SendEmail;

class SendMail extends Job
{
    
    
    /**
     * @param Email $email
     */
    protected $data;
    
    public function __construct($data)
    {
        $this->data = $data;
    }
    
    /**
     * @throws \Exception
     */
    public function handle()
    {
        SendEmail::send($this->data);
    }
}
