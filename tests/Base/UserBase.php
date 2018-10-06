<?php

namespace Test\Base;

use Laravel\Lumen\Testing\DatabaseTransactions;
use Test\TestCase;

class UserBase extends TestCase
{
    use DatabaseTransactions;

    protected $props = [
        ''
    ];
    public function __construct()
    {
        parent::__construct();
        $this->method = 'GET';
        $this->url    = 'profile';
    }

    public function testUserProfile()
    {
        $this->url .= '?include=pers';

        $option = [
            'headers' => $this->header,
        ];
        $body   = $this->request($option)->body();
        $data   = $body->getData();
//        dd($data);
        $this->assertTrue(true, 'Unable to get user profile');
        $this->assertEquals(200, $body->getCode());
    }
}