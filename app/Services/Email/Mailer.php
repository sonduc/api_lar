<?php
/**
 * Created by PhpStorm.
 * User: ducchien
 * Date: 07/11/2018
 * Time: 02:24
 */

namespace App\Services\Email;


use Carbon\Traits\Serialization;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;

class Mailer extends  Mailable
{
    use Queueable, Serialization;

    /**
     * The body of the message.
     *
     * @var string
     */
    public $content;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($content)
    {
        $this->content = $content;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email.blank')
            ->with('content', $this->content);
    }

}
