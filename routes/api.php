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

//Upload Image To S3
$router->post('upload-blog-image', 'UploadImageController@uploadBlogImage');
$router->post('upload-room-image', 'UploadImageController@uploadRoomImage');

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

    /**
     * BaoKim-Trade-History
     */

    $router->get('/baokim-trade', 'BaoKimTradeHistoryController@getBaoKimTradeList');
    $router->get('/baokim-trade/{id}', 'BaoKimTradeHistoryController@showBaoKimTrade');

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
    $router->get('/rooms/room-lat-long', 'RoomController@getRoomLatLong');
    $router->get('/rooms/room_recommend/{id}', 'RoomController@getRoomRecommend');

    $router->get('/rooms/type', 'RoomController@getRoomType');
    $router->get('/rooms/get-name', 'RoomController@getRoomName');
    $router->get('/rooms/media-type', 'RoomController@roomMediaType');
    $router->get('/rooms/rent-type', 'RoomController@roomRentType');
    $router->get('/rooms/room-status', 'RoomController@roomStatus');
    $router->put('/rooms/prop-update/{id}', 'RoomController@minorRoomUpdate');
    $router->get('/rooms/schedule/{id}', 'RoomController@getRoomSchedule');
    $router->get('/rooms/schedule-by-hour/{id}', 'RoomController@getRoomScheduleByHour');
    $router->put('/rooms/update-block', 'RoomController@updateRoomTimeBlock');
    $router->put('/rooms/update-setting', 'RoomController@updateRoomSettings');
    $router->put('/rooms/update-optional-prices', 'RoomController@updateRoomOptionalPrice');
    $router->put('/rooms/update-comission', 'RoomController@updateComission');
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
     * Host-Review-Customer
     */
    $router->put('/host-reviews/update-status/{id}', 'HostReviewController@updateStatus');
     resource('/host-reviews', 'HostReviewController', $router);

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
     * Transaction Resource
     */
    resource('/transactions', 'TransactionController', $router);
    $router->get('/transaction-types', 'TransactionController@transactionTypeList');
    $router->post('/transactions/combine-manual', 'TransactionController@combineTransaction');

    /**
     * Settings
     */
    $router->put('/settings/update-contact/{id}', 'SettingController@updateContact');
    $router->get('/settings/status', 'SettingController@settingStatus');
    resource('/settings', 'SettingController', $router);


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
    $router->get('/statisticals/booking-status', 'StatisticalController@bookingByStatusStatistical');
    $router->get('/statisticals/booking-city', 'StatisticalController@bookingByCityStatistical');
    $router->get('/statisticals/booking-district', 'StatisticalController@bookingByDistrictStatistical');
    $router->get('/statisticals/booking-type', 'StatisticalController@bookingByTypeStatistical');
    $router->get('/statisticals/booking-revenue', 'StatisticalController@bookingByRevenueStatistical');
    $router->get('/statisticals/booking-manager-revenue', 'StatisticalController@bookingByManagerRevenueStatistical');
    $router->get('/statisticals/booking-room-type-revenue', 'StatisticalController@bookingByRoomTypeRevenueStatistical');
    $router->get('/statisticals/count-booking-room-type', 'StatisticalController@bookingByRoomTypeStatistical');
    $router->get('/statisticals/booking-sex', 'StatisticalController@bookingBySexStatistical');
    $router->get('/statisticals/booking-price-range', 'StatisticalController@bookingByPriceRangeStatistical');
    $router->get('/statisticals/booking-age-range', 'StatisticalController@bookingByAgeRangeStatistical');
    $router->get('/statisticals/booking-source', 'StatisticalController@bookingBySourceStatistical');
    $router->get('/statisticals/booking-type-revenue', 'StatisticalController@bookingByTypeRevenueStatistical');
    $router->get('/statisticals/booking-cancel', 'StatisticalController@bookingByCancelStatistical');
    $router->get('/statisticals/room-type', 'StatisticalController@roomByTypeStatistical');
    $router->get('/statisticals/room-district', 'StatisticalController@roomByDistrictStatistical');
    $router->get('/statisticals/room-city', 'StatisticalController@roomByCityStatistical');
    $router->get('/statisticals/room-top-booking', 'StatisticalController@roomByTopBookingStatistical');
    $router->get('/statisticals/room-type-compare', 'StatisticalController@roomByTypeComparison');

    $router->get('/statisticals/booking-one-customer-revenue', 'StatisticalController@bookingByOneCustomerRevenueStatistical');
    $router->get('/statisticals/booking-type-one-customer', 'StatisticalController@bookingByTypeOneCustomerStatistical');
    
    $router->get('/statisticals/old-customer', 'StatisticalController@oldCustomerStatistical');

    //Compare checking
    resource('/compare-checking', 'CompareCheckingController', $router);
    $router->put('/compare-checking/prop-update/{id}', 'CompareCheckingController@minorCompareCheckingUpdate');

    /**
     *  Resource
     */
    resource('/topic', 'TopicController', $router);

    /**
     *  Resource
     */
    resource('/subtopic', 'SubTopicController', $router);

    /**
     *  Resource
     */
    $router->get('/ticket/status', 'TicketController@ticketStatus');
    $router->get('/ticket/supporter', 'TicketController@getSupporter');
    $router->put('/ticket/update-resolve/{id}', 'TicketController@updateResolve');
    $router->put('/ticket/update-supporter/{id}', 'TicketController@updateSupporter');
    resource('/ticket', 'TicketController', $router);

    /**
     *  comment-ticket
     */
    resource('/comment-tickets', 'CommentTicketController', $router);
});

$router->post('login', 'LoginController@login');
$router->post('register', 'RegisterController@register');

// Social login
$router->get('login/{social}', 'SocialAuthController@social');

//Calendar
$router->get('get-calendar/{id}', 'RoomCalendarController@getRoomCalendar');
// $router->post('update-calendar/{id}', 'RoomCalendarController@updateCalendar');

$router->get('update-merchant-role-db', 'RoleController@updateMerchantRole');



