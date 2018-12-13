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

$router->get('/bookings/booking-type-list', 'BookingController@bookingTypeList');
$router->get('/bookings/cancel-reason-list', 'BookingController@bookingCancelList');
$router->group([
    'middleware' => 'auth',
], function ($router) {
    /**
     * Booking-customer.
     */
    $router->get('/bookings', 'BookingController@index');
    $router->get('/bookings/{id}', 'BookingController@show');
    $router->post('/bookings/cancel-booking/{id}', 'BookingController@cancelBooking');
    $router->put('/bookings/status-update/{code}', 'BookingController@confirmBooking');

    /**
     * Profile Resource
     */
    $router->get('/profile', 'ProfileController@index');
    $router->put('/profile', 'ProfileController@update');
    $router->put('/profile/settings', 'ProfileController@settings');
    $router->put('/profile/change-password', 'ProfileController@changePassword');


    /**
     * Wish-list: Danh sách ưu thích
     */
    resource('/wish-list', 'WishListController', $router);
});

/*
 * Rooms Router
 */
$router->post('/rooms/room-lat-long', 'RoomController@getRoomLatLong');
$router->get('/rooms', 'RoomController@index');
$router->get('/rooms/{id}', 'RoomController@show');
$router->get('/rooms/schedule/{id}', 'RoomController@getRoomSchedule');
$router->get('/rooms/room_recommend/{id}', 'RoomController@getRoomRecommend');

/*
 * Booking Router
 */
$router->post('/bookings', 'BookingController@store');
$router->post('/bookings/price-calculator', 'BookingController@priceCalculator');

// thanh toan
$router->get('/bank-list/{uuid}', 'BookingController@bankList');
$router->post('/payment/{uuid}', 'BookingController@payment');
/**
 * Router login, register , reset pass, forget pass
 */
$router->post('login', 'LoginController@login');
$router->post('register', 'RegisterController@register');
$router->put('register/email-confirm', 'RegisterController@confirm');
$router->post('reset-password/{time}', 'ResetPasswordController@resetPassword');
$router->post('forget-password', 'ForgetPasswordController@forgetPassword');
$router->post('set-password/{time}', 'ResetPasswordController@resetPassword');
//// Social login
//$router->get('login/{social}', 'SocialAuthController@social');

/**
 * Router Coupon
 */
$router->post('coupons/calculate-discount', 'CouponController@calculateDiscount');
$router->get('coupons/status-list', 'CouponController@statusList');
$router->get('coupons/all-day-list', 'CouponController@allDayList');
resource('/coupons', 'CouponController', $router);

/**
 * Router Promotion
 */
$router->get('promotions/status-list', 'PromotionController@statusList');
resource('/promotions', 'PromotionController', $router);

/* thanh toan*/

$router->get('/success','PaymentHistoryController@success');
$router->get('/cancel/{code}','PaymentHistoryController@cancel');






