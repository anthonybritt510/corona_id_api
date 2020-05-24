<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', 'API\UserController@login');
Route::post('register', 'API\UserController@register');

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('userDetails', 'API\UserController@userDetails');
    Route::post('sendVerifyEmail', 'API\UserController@sendVerifyEmail');
    Route::post('confirmVerifyEmail', 'API\UserController@confirmVerifyEmail');
    Route::post('getUserInfo', 'API\UserController@getUserInfo');
    Route::post('setOrderInfo', 'API\UserController@setOrderInfo');

    Route::post('addTest', 'API\TestController@addTest');
    Route::post('getTestsByPatient', 'API\TestController@getTestsByPatient');
    Route::post('getTestsByProfessinoal', 'API\TestController@getTestsByProfessinoal');

    Route::post('getNotifications', 'API\NotificationController@getNotifications');

    Route::post('createOrder', 'API\OrderController@createOrder');
    Route::post('getOrders', 'API\OrderController@getOrders');
});
