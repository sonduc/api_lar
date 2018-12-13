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
});

resource('/test', 'TestController', $router);

