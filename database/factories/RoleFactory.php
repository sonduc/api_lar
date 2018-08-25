<?php

$factory->define(App\Repositories\Roles\Role::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'slug' => $faker->slug,
        'permissions' => []
    ];
});
