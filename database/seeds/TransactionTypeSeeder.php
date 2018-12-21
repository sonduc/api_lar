<?php

use Illuminate\Database\Seeder;

class TransactionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('transaction_type')->insert([
            [
                'name' => 'Booking',
            ],
            
            [
                'name' => 'Phạt host',
            ],

            [
                'name' => 'Phụ thu',
            ],

            [
                'name' => 'Giảm giá',
            ],

            [
                'name' => 'Phiếu chi',
            ],

            [
                'name' => 'Thưởng host',
            ],

            [
                'name' => 'Book Airbnb',
            ],
            
            [
                'name' => 'Book Booking',
            ],

            [
                'name' => 'Book Agoda',
            ],
            
            [
                'name' => 'Phiếu thu',
            ],
        ]);
    }
}
