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
     * Get config
     */
    $router->get('/permissions', 'PermissionController@index');
    $router->get('/languages', 'LanguageController@index');

    /**
     * Log Resource
     */
    resource('/logs', 'LogController', $router);


    /**
     * User Resource
     */
    $router->get('/users/sex-list', 'UserController@sexList');
    $router->get('/users/level-list', 'UserController@levelList');
    $router->get('/users/account-type-list', 'UserController@accountTypeList');
    resource('/users', 'UserController', $router);
    /**
     * Profile Resource
     */
    $router->get('/profile', 'ProfileController@index');
    $router->put('/profile', 'ProfileController@update');
    $router->put('/profile/change-password', 'ProfileController@changePassword');


    $router->get('/permissions', 'PermissionController@index');
    /**
     * Role Resource
     */
    resource('/roles', 'RoleController', $router);

    /**
     * Room Resource
     */
    $router->post('/rooms/room-lat-long', 'RoomController@getRoomLatLong');
    $router->get('/rooms/room_recommend/{id}', 'RoomController@getRoomRecommend');
    $router->get('/rooms/type', 'RoomController@getRoomType');
    $router->get('/rooms/get-name', 'RoomController@getRoomName');
    $router->get('/rooms/media-type', 'RoomController@roomMediaType');
    $router->get('/rooms/rent-type', 'RoomController@roomRentType');
    $router->get('/rooms/room-status', 'RoomController@roomStatus');
    $router->put('/rooms/prop-update/{id}', 'RoomController@minorRoomUpdate');
    $router->get('/rooms/schedule/{id}', 'RoomController@getRoomSchedule');
    $router->put('/rooms/update-block', 'RoomController@updateRoomTimeBlock');
    $router->put('/rooms/update-setting', 'RoomController@updateRoomSettings');
    resource('/rooms', 'RoomController', $router);
    /**
     * Room Review Resource
     */
    $router->get('/reviews/reviews-status-list', 'RoomReviewController@reviewStatusList');
    $router->get('/reviews/reviews-like-list', 'RoomReviewController@reviewLikeList');
    $router->get('/reviews/reviews-service-list', 'RoomReviewController@reviewServiceList');
    $router->get('/reviews/reviews-quality-list', 'RoomReviewController@reviewQualityList');
    $router->get('/reviews/reviews-cleanliness-list', 'RoomReviewController@reviewCleanlinessList');
    $router->get('/reviews/reviews-valuable-list', 'RoomReviewController@reviewValuableList');
    $router->get('/reviews/reviews-recommend-list', 'RoomReviewController@reviewRecommendList');
    resource('/reviews', 'RoomReviewController', $router);




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
     * Booking Resource
     */
    $router->get('/bookings/booking-status-list', 'BookingController@bookingStatusList');
    $router->get('/bookings/booking-type-list', 'BookingController@bookingTypeList');
    $router->get('/bookings/type-list', 'BookingController@typeList');
    $router->get('/bookings/payment-method-list', 'BookingController@paymentMethodList');
    $router->get('/bookings/payment-status-list', 'BookingController@paymentStatusList');
    $router->get('/bookings/payment-history-type-list', 'BookingController@paymentHistoryTypeList');
    $router->get('/bookings/booking-source-list', 'BookingController@bookingSourceList');
    $router->get('/bookings/price-range-list', 'BookingController@priceRangeList');
    $router->get('/bookings/cancel-reason-list', 'BookingController@bookingCancelList');
    $router->post('/bookings/price-calculator', 'BookingController@priceCalculator');
    $router->put('/bookings/status-update/{id}', 'BookingController@minorBookingUpdate');
    $router->put('/bookings/money-update/{id}', 'BookingController@updateBookingMoney');
    $router->post('/bookings/cancel-booking/{id}', 'BookingController@cancelBooking');

    resource('/bookings', 'BookingController', $router);
    /**
     * Payment History
     */
    $router->get('/payments/payment-history-status', 'PaymentHistoryController@paymentHistoryStatus');
    resource('/payments', 'PaymentHistoryController', $router);

    /**
     * Category Resource
     */
    $router->get('/categories/status-list', 'CategoryController@statusList');
    $router->get('/categories/hot-list', 'CategoryController@hotList');
    $router->get('/categories/new-list', 'CategoryController@hotList');
    $router->put('/categories/single-update/{id}', 'CategoryController@singleUpdate');
    resource('/categories', 'CategoryController', $router);

    /**
     * Blogs Resource
     */
    $router->get('/blogs/status-list', 'BlogController@statusList');
    $router->get('/blogs/hot-list', 'BlogController@hotList');
    $router->get('/blogs/new-list', 'BlogController@newList');
    $router->put('/blogs/single-update/{id}', 'BlogController@singleUpdate');
    resource('/blogs', 'BlogController', $router);

    /**
     * Collections Resource
     */
    $router->get('/collections/status-list', 'CollectionController@statusList');
    $router->get('/collections/hot-list', 'CollectionController@hotList');
    $router->get('/collections/new-list', 'CollectionController@newList');
    $router->put('/collections/single-update/{id}', 'CollectionController@singleUpdate');
    resource('/collections', 'CollectionController', $router);


    /**
     * Promotions Resource
     */
    $router->put('/promotions/single-update/{id}', 'PromotionController@singleUpdate');
    $router->get('/promotions/status-list', 'PromotionController@statusList');
    resource('/promotions', 'PromotionController', $router);

    /**
     * Coupons Resource
    */
    $router->get('/coupons/status-list', 'CouponController@statusList');
    $router->get('/coupons/all-day-list', 'CouponController@allDayList');
    $router->put('/coupons/single-update/{id}', 'CouponController@singleUpdate');
    $router->post('/coupons/calculate-discount', 'CouponController@calculateDiscount');
    resource('/coupons', 'CouponController', $router);

    /**
     * EmailCustomer Resource
     */
    $router->get('/emailcustomers/booking-success', 'EmailCustomerController@bookingSuccess');
    $router->get('/emailcustomers/user-owner', 'EmailCustomerController@userOwner');
    $router->get('/emailcustomers/booking-checkout', 'EmailCustomerController@bookingCheckout');

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
     * Statistical Resource
     */
    $router->get('/statisticals/booking', 'StatisticalController@bookingStatistical');
    $router->get('/statisticals/booking-city', 'StatisticalController@statisticalCity');
    $router->get('/statisticals/booking-district', 'StatisticalController@statisticalDistrict');
});


$router->post('login', 'LoginController@login');
$router->post('register', 'RegisterController@register');

// Social login
$router->get('login/{social}', 'SocialAuthController@social');
