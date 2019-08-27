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


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::post('login', 'AuthController@login');
Route::get('logout', 'AuthController@logout');
Route::post('new', 'UserController@create');
Route::get('me', 'UserController@edit');
Route::post('user', 'UserController@update');
Route::delete('user', 'UserController@delete');

