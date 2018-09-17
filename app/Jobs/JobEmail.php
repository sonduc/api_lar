<?php

namespace App\Jobs;

use App\Services\Email\SendEmail;
use Illuminate\Support\Facades\Mail;

class JobEmail extends Job
{


    /**
     * @param Email $email
     */
    protected $email;
    public function __construct($email)
    {
        $this->email = $email;
    }

    /**
     * @throws \Exception
     */
    public function handle()
    {
       SendEmail::send($this->email);
    }
}
