<?php

namespace Test;

use Laravel\Lumen\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic test example.
     *
     * @return void
     */
    protected $props = [
        'id',
        'uuid',
        'name',
        'email',
        'gender',
        'birthday',
        'address',
        'phone',
        'avatar',
        'level',
        'vip',
        'point',
        'money',
        'status',
        'type',
    ];

    public function testUserResponse()
    {
        $option = [
            'headers' => $this->header,
        ];

        $this->method = 'GET';
        $this->url    = 'users';

        $body   = $this->request($option)->body();
        $status = true;

        $data = $body->getData();
        foreach ($data as $item) {
            if (!$this->checkResponseData($item, $this->props)) {
                $status = false;
                break;
            }
        }

        $this->assertTrue($status, 'Lỗi user.index trả về kết quả không đúng định dạng!');
        $this->assertEquals(200, $body->getCode());
    }
}
