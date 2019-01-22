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

    /**
     *
     */
    $router->put('/bookings/status-update/{id}', 'BookingController@updateBookingStatus');
    $router->put('/bookings/money-update/{id}', 'BookingController@updateBookingMoney');
    $router->get('/bookings/booking-status-list', 'BookingController@bookingStatusList');
    $router->get('/bookings', 'BookingController@index');
    $router->get('/bookings/{id}', 'BookingController@show');


    /**
     * Profile
     */
    $router->get('/profile', 'ProfileController@index');
    $router->put('/profile', 'ProfileController@update');
    $router->put('/profile/change-password', 'ProfileController@changePassword');

    /**
     * City Resource
     */
    resource('/cities', 'CityController', $router);

    /**
     * District Resource
     */
    resource('/districts', 'DistrictController', $router);

    /**
     * Comfort Resource
     */
    resource('/comforts', 'ComfortController', $router);

    /**
     * GuidebookCategory Resource
     */
    resource('/guidebookcategories', 'GuidebookCategoryController', $router);

    /**
     * Place Resource
     */
    $router->post('/places/update-room-place', 'PlaceController@editRoomPlace');
    $router->put('/places/single-update/{id}', 'PlaceController@singleUpdate');
    $router->get('/places/status-list', 'PlaceController@statusList');
    resource('/places', 'PlaceController', $router);


    /**
     * Room Resource
     */

    $router->get('/rooms/type', 'RoomController@getRoomType');
    $router->get('/rooms/get-name', 'RoomController@getRoomName');
    $router->get('/rooms/media-type', 'RoomController@roomMediaType');
    $router->get('/rooms/rent-type', 'RoomController@roomRentType');

    $router->put('/rooms/update-block', 'RoomController@updateRoomTimeBlock');
    $router->put('/rooms/update-setting', 'RoomController@updateRoomSettings');
    $router->put('/rooms/update-optional-prices', 'RoomController@updateRoomOptionalPrice');
    resource('/rooms', 'RoomController', $router);

    /**
     * Promotion Resource
     */
    $router->post('/promotions/join-promotion', 'PromotionController@joinPromotion');
    resource('promotions', 'PromotionController', $router);

    /**
     *  Resource
     */
    $router->get('/ticket/status', 'TicketController@ticketStatus');
    resource('/ticket', 'TicketController', $router);

    /**
     *  comment-ticket
     */
    resource('/comment-tickets', 'CommentTicketController', $router);
});

/**
 * Router login, register , reset pass, forget pass
 */
$router->post('login', 'LoginController@login');
$router->post('register', 'RegisterController@register');
$router->put('register/email-confirm/{uuid}', 'RegisterController@confirm');
$router->post('reset-password/{time}', 'ResetPasswordController@resetPassword');
$router->get('set-password/{time}', 'ResetPasswordController@getFormResetPassword');
$router->post('forget-password', 'ForgetPasswordController@forgetPassword');

resource('/test', 'TestController', $router);

