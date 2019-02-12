<?php

use Illuminate\Database\Seeder;

class BlogCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories')->insert([
            [
                'id'        => 1,
                'image'     => '',
                'hot'       => 1,
                'new'       => 1,
                'status'    => 1
            ],
            
            [
                'id'        => 2,
                'image'     => '',
                'hot'       => 1,
                'new'       => 1,
                'status'    => 1
            ],

            [
                'id'        => 3,
                'image'     => '',
                'hot'       => 1,
                'new'       => 1,
                'status'    => 1
            ],

            [
                'id'        => 4,
                'image'     => '',
                'hot'       => 1,
                'new'       => 1,
                'status'    => 1
            ],

            [
                'id'        => 5,
                'image'     => '',
                'hot'       => 1,
                'new'       => 1,
                'status'    => 1
            ]
        ]);

        DB::table('categories_translate')->insert([
            [
                'id'            => 1,
                'name'          => 'Ăn gì',
                'slug'          => 'an-gi',
                'lang'          => 'vi',
                'category_id'   => 1
            ],
            [
                'id'            => 2,
                'name'          => 'Chơi gì',
                'slug'          => 'choi-gi',
                'lang'          => 'vi',
                'category_id'   => 2
            ],
            [
                'id'            => 3,
                'name'          => 'Ở đâu',
                'slug'          => 'o-dau',
                'lang'          => 'vi',
                'category_id'   => 3
            ],
            [
                'id'            => 4,
                'name'          => 'Điểm đến',
                'slug'          => 'diem-den',
                'lang'          => 'vi',
                'category_id'   => 4
            ],
            [
                'id'            => 5,
                'name'          => 'Giải trí',
                'slug'          => 'giai-tri',
                'lang'          => 'vi',
                'category_id'   => 5
            ],
        ]);
    }
}
