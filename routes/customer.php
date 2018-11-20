<?php

/*
|--------------------------------------------------------------------------
| Common api Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
$router->group([
    'middleware' => 'auth',
], function ($router) {
    $router->get('/rooms', 'RoomController@index');
    $router->get('/rooms/{id}', 'RoomController@show');
    $router->get('/rooms/schedule/{id}', 'RoomController@getRoomSchedule');

    /**
     * Booking-customer.
     */
    $router->get('/bookings', 'BookingController@index');
    $router->post('/bookings/cancel-booking/{id}', 'BookingController@cancelBooking');
    $router->put('/bookings/status-update/{code}', 'BookingController@confirmBooking');
});
    $router->post('/bookings/price-calculator', 'BookingController@priceCalculator');
    $router->post('/bookings', 'BookingController@store');



/**
 * Router login, register
 */
$router->post('login', 'LoginController@login');
$router->post('register', 'RegisterController@register');
$router->put('register/email-confirm', 'RegisterController@confirm');
//// Social login
//$router->get('login/{social}', 'SocialAuthController@social');

