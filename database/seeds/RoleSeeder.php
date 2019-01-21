<?php
/**
 * Created by PhpStorm.
 * User: DUCCHIEN-PC
 * Date: 1/21/2019
 * Time: 11:17 AM
 */

use Illuminate\Database\Seeder;
class RoleSeeder extends Seeder
{
    public function run()
    {

        if (!\App\Repositories\Roles\Role::find(1)) {
            factory(App\Repositories\Roles\Role::class)->create([
                'name'        => 'Super admin',
                'slug'        => 'superadmin',
                'permissions' => [
                    'admin.super-admin' => true,
                ],
            ]);
        }

        if (!\App\Repositories\Roles\Role::find(2)) {
            factory(App\Repositories\Roles\Role::class)->create([
                'name'        => 'Supporter',
                'slug'        => 'supporter',
                'permissions' => [
                    "ticket.view"                 => true,
                    "ticket.create"               => true,
                    "ticket.update"               => true,
                    "ticket.delete"               => true,
                ],
            ]);
        }



        if (!\App\Repositories\Roles\Role::find(3)) {
            factory(App\Repositories\Roles\Role::class)->create([
                'name'                  => 'Merchant',
                'slug'                  => 'merchant',
                'permissions'           => [
                    "room.view"                 => true,
                    "room.create"               => true,
                    "room.update"               => true,
                    "room.delete"               => true,
                    "room.export"               => true,
                    "booking.view"              => true,
                    "booking.create"            =>true,
                    "booking.update"            => true,
                    "booking.cancel"            => true,
                    "statistics.view"           => true,
                    "promotion.view"            => true,
                    "coupon.view"               => true,
                    "coupon.create"             => true,
                    "coupon.update"             => true,
                    "coupon.delete"             => true,
                    "guidebookcategory.view"    => true,
                    "place.view"                => true,
                    "place.update"              => true,
                    "place.create"              => true,
                    "city.view"                 => true,
                    "comfort.view"              =>true,
                    "district.view"             => true
                ],
            ]);
        }




    }

}