<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/create_user','AuthController@create_user');
Route::get('/user_info','AuthController@user_info');
Route::get('/login','AuthController@login');
Route::post('/post_services','AuthController@post_services');
Route::get('/get_token','AuthController@get_token');
Route::get('/get_services','AuthController@get_services');
