<?php

require_once __DIR__ . '/../vendor/autoload.php';

try {
    (new Dotenv\Dotenv(__DIR__ . '/../'))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    //
}

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/


$app = new Laravel\Lumen\Application(
    realpath(__DIR__ . '/../')
);

// Enable Facades
$app->withFacades();
// Enable Eloquent
$app->withEloquent();

// Config file
// $app->configure('app');
$app->configure('cors');
$app->configure('auth');
$app->configure('singleton');
$app->configure('permissions');
$app->configure('regex');
$app->configure('languages');
$app->configure('activitylog');
//$app->configure('services');// dùng cho mialgun
$app->configure('mail');
$app->configure('broadcasting');

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);


/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

$app->middleware([
    \Barryvdh\Cors\HandleCors::class,
    //App\Http\Middleware\ExampleMiddleware::class,
]);

// Enable auth middleware (shipped with Lumen)
$app->routeMiddleware([
    'auth' => App\Http\Middleware\Authenticate::class,
]);

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/


$app->register(App\Providers\AppServiceProvider::class);
$app->register(App\Providers\AuthServiceProvider::class);
$app->register(App\Providers\ValidatorServiceProvider::class);
$app->register(App\Providers\EventServiceProvider::class);


$app->register(Laravel\Passport\PassportServiceProvider::class);
$app->register(Dusterio\LumenPassport\PassportServiceProvider::class);
$app->register(ElfSundae\Laravel\Hashid\HashidServiceProvider::class);
$app->register(Barryvdh\Cors\ServiceProvider::class);

$app->register(HarikiRito\ApiGenerator\ApiGeneratorServiceProvider::class);
$app->register(Spatie\Activitylog\ActivitylogServiceProvider::class);

$app->register(Illuminate\Redis\RedisServiceProvider::class);
//if (env('APP_DEBUG')) {
//    $app->register(Barryvdh\Debugbar\LumenServiceProvider::class);
//}

/*
|--------------------------------------------------------------------------
| load the config
|--------------------------------------------------------------------------
|
| Here we will load all config from /config forder
|
*/


/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

$app->router->group([
    'namespace' => 'App\Http\Controllers',
], function ($router) {
    require_once __DIR__ . '/../routes/web.php';
});

// Admin API
$app->router->group([
    'prefix'    => 'api',
    'namespace' => 'App\Http\Controllers\Api',
], function ($router) {
    require_once __DIR__ . '/../routes/api.php';
});

// Customer API
$app->router->group([
    'prefix'    => 'customer-api',
    'namespace' => 'App\Http\Controllers\ApiCustomer',
], function ($router) {
    require_once __DIR__ . '/../routes/customer.php';
});

// Merchant API
$app->router->group([
    'prefix'    => 'merchant-api',
    'namespace' => 'App\Http\Controllers\ApiMerchant',
], function ($router) {
    require_once __DIR__ . '/../routes/merchant.php';
});

return $app;
