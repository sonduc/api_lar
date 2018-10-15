<?php

namespace App\Providers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class ValidatorServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    public function boot()
    {
        Validator::extend('v_title', 'App\Validator\VietnameseNameValidator@passes');
        Validator::extend('guest_check', 'App\Validator\NumberOfGuestsValidator@check');
    }
}
