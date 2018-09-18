<?php

namespace App\Jobs;

use App\Services\Email\SendEmail;
use Illuminate\Support\Facades\Mail;

class JobEmail extends Job
{


    /**
     * @param Email $email
     */
    protected $data;
    public function __construct($data)
    {
//        dd($data);
        $this->data = $data;
    }

    /**
     * @throws \Exception
     */
    public function handle()
    {

//        dd($this->data);
       SendEmail::send($this->data);
    }
}
