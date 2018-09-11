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

        DB::table('oauth_clients')->insert($this->oauthDev());
    }

    public function oauthDev()
    {
        return $oauth_clients = array(
            array(
                "id" => 1,
                "user_id" => NULL,
                "name" => " Personal Access Client",
                "secret" => "gCRLsLTDmDOCmgnilqlTqOwXDkNTrczTGKA4XVEE",
                "redirect" => "http://localhost",
                "personal_access_client" => 1,
                "password_client" => 0,
                "revoked" => 0,
                "created_at" => "2018-09-08 16:21:59",
                "updated_at" => "2018-09-08 16:21:59",
            ),
            array(
                "id" => 2,
                "user_id" => NULL,
                "name" => " Password Grant Client",
                "secret" => "HB8ktAlp3O9MgD0nAbzsuq8nyvPx8ZLdZmzOtVIf",
                "redirect" => "http://localhost",
                "personal_access_client" => 0,
                "password_client" => 1,
                "revoked" => 0,
                "created_at" => "2018-09-08 16:21:59",
                "updated_at" => "2018-09-08 16:21:59",
            ),
        );

    }
}
