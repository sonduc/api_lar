<?php

namespace App\Providers;

use App\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Dusterio\LumenPassport\LumenPassport;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        \App\User::class                                => \App\Policies\UserPolicy::class,
        \App\Repositories\Roles\Role::class             => \App\Policies\RolePolicy::class,
        \App\Repositories\Rooms\Room::class             => \App\Policies\RoomPolicy::class,
        \App\Repositories\Cities\City::class            => \App\Policies\CityPolicy::class,
        \App\Repositories\Districts\District::class     => \App\Policies\DistrictPolicy::class,
        \App\Repositories\Comforts\Comforts::class      => \App\Policies\ComfortPolicy::class,
        \App\Repositories\Logs\Log::class               => \App\Policies\LogPolicy::class,
    ];

    /**
     * Register the application's policies.
     *
     * @return void
     */
    public function registerPolicies()
    {
        foreach ($this->policies as $key => $value) {
            Gate::policy($key, $value);
        }
    }

    /**
     * Get the policies defined on the provider.
     *
     * @return array
     */
    public function policies()
    {
        return $this->policies;
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Register passport
     * @return void
     */
    private function registerPassport()
    {
        LumenPassport::routes($this->app);
        LumenPassport::tokensExpireIn(\Carbon\Carbon::now()->addYears(1));
        LumenPassport::allowMultipleTokens();
    }

    /**
     * Register gates
     * @return void
     */
    private function registerGates()
    {
        // user
//        Gate::define('user.view', 'App\Policies\UserPolicy@view');
//        Gate::define('user.create', 'App\Policies\UserPolicy@create');
//        Gate::define('user.update', 'App\Policies\UserPolicy@update');
//        Gate::define('user.delete', 'App\Policies\UserPolicy@delete');
//        // role gate
//        Gate::define('role.view', 'App\Policies\RolePolicy@view');
//        Gate::define('role.create', 'App\Policies\RolePolicy@create');
//        Gate::define('role.update', 'App\Policies\RolePolicy@update');
//        Gate::define('role.delete', 'App\Policies\RolePolicy@delete');
//        // room gate
//        Gate::define('room.view', 'App\Policies\RoomPolicy@view');
//        Gate::define('room.create', 'App\Policies\RoomPolicy@create');
//        Gate::define('room.update', 'App\Policies\RoomPolicy@update');
//        Gate::define('room.delete', 'App\Policies\RoomPolicy@delete');
//        // city gate
//        Gate::define('city.view', 'App\Policies\CityPolicy@view');
//        Gate::define('city.create', 'App\Policies\CityPolicy@create');
//        Gate::define('city.update', 'App\Policies\CityPolicy@update');
//        Gate::define('city.delete', 'App\Policies\CityPolicy@delete');
        $permissions = config('permissions');
        foreach ($permissions as $key => $role) {
            if ($key !== 'admin') {
                foreach ($role['list'] as $key_role => $per) {
                    Gate::define("{$key}.{$key_role}", 'App\Policies\\'.ucfirst($key).'Policy@'.$key_role);
                }
            }
        }

    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        // $this->app['auth']->viaRequest('api', function ($request) {
        //     if ($request->input('api_token')) {
        //         return User::where('api_token', $request->input('api_token'))->first();
        //     }
        // });
        //
        //
        //

        $this->register();
        $this->registerPassport();
        $this->registerPolicies();
        $this->registerGates();
    }
}
