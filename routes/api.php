<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;


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
    //person
    Route::post('add_new_person', 'PersonController@store');
    Route::put('edit_person', 'PersonController@update');
    Route::post('delete_person', 'PersonController@delete');
    Route::get('get_person', 'PersonController@index');
    Route::get('get_person_email', 'PersonController@index_email');
    //user
    Route::post('add_new_user', 'UserController@store');
    Route::put('edit_user', 'UserController@update');
    Route::delete('delete_user', 'UserController@delete');
    Route::get('get_user', 'UserController@index');
    Route::get('get_user_id', 'UserController@index_id');
    //event
    Route::post('add_new_event', 'EventController@store');
    Route::put('edit_event', 'EventController@update');
    Route::delete('delete_event', 'EventController@delete');
    Route::get('get_event', 'EventController@index');
    Route::get('get_event_id', 'EventController@index_id');

