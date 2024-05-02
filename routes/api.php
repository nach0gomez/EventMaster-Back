<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\AuthController;


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

    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('register', 'AuthController@register');
    Route::post('login', 'AuthController@authenticate');
    Route::get('test', 'TestController@test');
    Route::post('add_new_person', 'PersonController@store');
    Route::put('edit_person', 'PersonController@update');
    Route::post('delect_person', 'PersonController@destroy');
    Route::get('get_person', 'PersonController@index');
    Route::get('get_person_id', 'PersonController@index_id');
    Route::post('add_new_user', 'UserController@store');
    Route::put('edit_user', 'UserController@update');
    Route::post('delect_user', 'UserController@delete');
    Route::get('get_user', 'UserController@index');
    Route::get('get_user_id', 'UserController@index_id');

