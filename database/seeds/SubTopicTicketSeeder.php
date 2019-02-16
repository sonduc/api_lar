<?php

use Illuminate\Database\Seeder;

class SubTopicTicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sub_topics')->insert([
            [
                'topic_id' => 1,
                'name' => 'Trở thành chủ nhà'
            ],

            [
                'topic_id' => 9,
                'name' => 'Chương trình khuyến mãi'
            ],

            [
                'topic_id' => 2,
                'name' => 'Chính sách hoàn & Hủy'
            ],

            [
                'topic_id' => 3,
                'name' => 'Quản lý nhân viên'
            ],

            [
                'topic_id' => 4,
                'name' => 'Quản lý phòng',
            ],
            
            [
                'topic_id' => 5,
                'name' => 'Khiếu nại'
            ],

            [
                'topic_id' => 5,
                'name' => 'Thông tin đối soát'
            ],

            [
                'topic_id' => 2,
                'name' => 'Quản lý đặt phòng'
            ],

            [
                'topic_id' => 2,
                'name' => 'Thanh toán'
            ],

            [
                'topic_id' => 1,
                'name' => 'Khác'
            ],
        ]);
    }
}
