<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 1/29/2019
 * Time: 3:52 AM
 */

namespace App\Listeners\SendMail;

use App\Events\Host_Reviews_Event;
use App\Services\Email\SendEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
class HostReviewListener implements ShouldQueue
{
    protected $email;
    /**
     * Create the event listener.
     *
     * @return void
     */

    public function __construct(SendEmail $email)
    {
        $this->email = $email;
    }


    public function handle(Host_Reviews_Event $event)
    {
        $this->email->mailHostReview($event->data);
    }

}