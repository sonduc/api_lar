<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!\App\User::find(1)) {
            factory(\App\User::class)->create([
                'name'     => 'SupperAdmin',
                'email'    => 'admin@westay.org',
                'password' => 'admin'
            ]);
        }

        if (!\App\Repositories\Roles\Role::find(1)) {
            factory(App\Repositories\Roles\Role::class)->create([
                'name' => 'Super admin',
                'slug' => 'superadmin',
                'permissions' => [
                    'admin.super-admin' => true
                ]
            ]);
        }
        if (!DB::table('role_users')->where('user_id', 1)->where('role_id', 1)->first()) {
            DB::table('role_users')->insert(['user_id' => 1, 'role_id' => 1]);
        }
    }
}
