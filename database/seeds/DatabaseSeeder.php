<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call('RoleSeeder');
        $this->call('UserSeeder');
        $this->call('GuidebookCategorySeeder');
        $this->call('TransactionTypeSeeder');
        $this->call('SubTopicTicketSeeder');
        $this->call('TopicTicketSeeder');
        $this->call('BlogCategorySeeder');
    }
}
