<?php

namespace Test\Base;

use Laravel\Lumen\Testing\DatabaseTransactions;
use Test\Roles\Roles;
use Test\TestCase;

class UserBase extends TestCase
{
    use DatabaseTransactions;

    protected $props = [
        'id','uuid','name','email','gender', 'birthday', 'level', 'vip', 'point', 'money', 'status', 'type'
    ];
    public function __construct()
    {
        parent::__construct();
        $this->method = 'GET';
        $this->url    = 'profile';
    }

    public function testUserProfile()
    {
        $this->loginAs(Roles::ADMIN);

        $this->url .= '?include=pers';
        $option = [
            'headers' => $this->header,
        ];

        $body   = $this->request($option)->body();
        $data   = $body->getData();

        $status = true;
        if (!$this->checkResponseData($data, $this->props)) {
            $status = false;
        }

        $this->assertTrue($status, 'Unable to get user profile');
        $this->assertEquals(200, $body->getCode());
    }
}