<?php

use Illuminate\Database\Seeder;

class TopicTicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('topics')->insert([
            [
                'id'   => 1,
                'name' => 'Trở thành chủ nhà'
            ],

            [
                'id'   => 2,
                'name' => 'Chương trình khuyến mãi'
            ],

            [
                'id'   => 3,
                'name' => 'Chính sách hoàn & Hủy'
            ],

            [
                'id'   => 4,
                'name' => 'Quản lý nhân viên'
            ],

            [
                'id'   => 5,
                'name' => 'Quản lý phòng',
            ],
            
            [
                'id'   => 6,
                'name' => 'Khiếu nại'
            ],

            [
                'id'   => 7,
                'name' => 'Thông tin đối soát'
            ],

            [
                'id'   => 8,
                'name' => 'Quản lý đặt phòng'
            ],

            [
                'id'   => 9,
                'name' => 'Thanh toán'
            ],

            [
                'id'   => 10,
                'name' => 'Khác'
            ],
        ]);
    }
}
