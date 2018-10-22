<?php

namespace App\Providers;

use App\Container\DIContainer;
use Illuminate\Auth\AuthManager;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public $container;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            'mailer',
            function ($app) {
                return $app->loadComponent('mail', 'Illuminate\Mail\MailServiceProvider', 'mailer');
            }
        );

        // Binding interface with repository
        foreach (config('singleton') as $item) {
            $this->app->singleton($item[0], $item[1]);
        }
        // Aliases
        $this->app->alias('mailer', \Illuminate\Contracts\Mail\Mailer::class);

    }

    public function boot()
    {
        $this->app->singleton(AuthManager::class, function ($app) {
            return $app->make('auth');
        });
    }
}
