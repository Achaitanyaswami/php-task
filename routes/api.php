<?php

use Illuminate\Http\Request;

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

Route::post('login', 'API\UserController@login');
Route::post('register', 'API\UserController@register');
Route::get('clear_rejected', 'API\UserController@clear_rejected');


Route::group(['middleware' => 'auth:api'], function()
{
	Route::post('details', 'API\UserController@details');
});
Route::group(['middleware' =>['auth:api', 'VerifyProvider']], function () 
{
	Route::post('post_services', 'API\UserController@post_services');
	Route::get('get_services', 'API\UserController@get_services');
	Route::get('get_request', 'API\UserController@get_providers_request');
	Route::post('update_request', 'API\UserController@update_request');
});

Route::group(['middleware' =>['auth:api', 'VerifyCustomer']], function () 
{
	Route::get('nearest_provider/{radius}', 'API\UserController@find_nearest_provider');	
	Route::post('send_request', 'API\UserController@send_request_provider');
});