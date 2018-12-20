<?php

namespace App\Listeners\AWS;

use App\Events\AmazonS3_Upload_Event;
use App\Services\Amazon\S3\ImageProcessor;
use App\Services\Amazon\S3\S3Processor;
use Illuminate\Contracts\Queue\ShouldQueue;

class S3UploadListener implements  ShouldQueue
{
    protected $img;
    /**
     * Create the event listener.
     *
     * @return void
     */
    private $processor;
    private $s3;

    public function __construct(ImageProcessor $processor, S3Processor $s3)
    {
        $this->processor = $processor;
        $this->s3        = $s3;
    }

    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param AmazonS3_Upload_Event $event
     *
     * @throws \Exception
     */
    public function handle(AmazonS3_Upload_Event $event)
    {
        $this->processor->setImage($event->name, $event->data);
        $this->s3Upload();
    }


    public function s3Upload()
    {

        $this->processor->setQuality(90);
//        $this->processor->setWidth(1280);
        $this->processor->setFormat('jpg');

        $name = $this->processor->getFullName();
        $mime = $this->processor->getMime();
        $img = $this->processor->getEncoded();

//        $this->s3->putObject($name, $img, $mime);
//        $this->s3->deleteObjects(['abc.webp']);
    }
}
