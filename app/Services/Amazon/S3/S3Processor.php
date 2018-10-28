<?php

namespace App\Services\Amazon\S3;

use Aws\S3\S3Client;

class S3Processor
{
    public const ACL_LIST = [
        'private',
        'public-read',
        'public-read-write',
        'aws-exec-read',
        'authenticated-read',
        'bucket-owner-read',
        'bucket-owner-full-control',
        'log-delivery-write',
    ];

    protected $processor;
    protected $ACL;
    protected $body;
    protected $name;
    protected $contentType;
    protected $bucket;
    protected $image;

    public function __construct()
    {
        $this->processor = new S3Client([
            'version' => 'latest',
            'region'  => env('AWS_REGION'),
        ]);

        $this->bucket = env('AWS_BUCKET');
        $this->ACL    = 'public-read';
    }

    /**
     *
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param $ACL
     *
     * @throws \Exception
     */
    public function setACL($ACL): void
    {
        if (in_array($ACL, self::ACL_LIST)) {
            $this->ACL = $ACL;
        }

        throw new \Exception('Access Control List (ACL) for Amazon S3 is not valid');
    }

    /**
     * @param mixed $body
     */
    public function setBody($body): void
    {
        $this->body = $body;
    }

    /**
     * @param mixed $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param mixed $contentType
     */
    public function setContentType(string $contentType): void
    {
        $this->contentType = $contentType;
    }

    /**
     * @param mixed $bucket
     */
    public function setBucket(string $bucket): void
    {
        $this->bucket = $bucket;
    }

    /**
     * Put a single object to the bucket
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param string $name
     * @param        $body
     * @param string $mime
     */
    public function putObject(string $name, $body, string $mime): void
    {
        $this->processor->putObject([
            'Bucket'      => $this->bucket,
            'ContentType' => $mime,
            'Key'         => $name,
            'Body'        => $body,
            'ACL'         => $this->ACL,
        ]);
    }

    /**
     * Delete objects from S3 bucket
     * @author HarikiRito <nxh0809@gmail.com>
     *
     * @param array $keys
     */
    public function deleteObjects(array $keys): void
    {
        $this->processor->deleteObjects([
            'Bucket' => $this->bucket,
            'Delete' => [
                'Objects' => array_map(function ($key) {
                    return [
                        'Key' => $key,
                    ];
                }, $keys),
            ],
        ]);
    }
}