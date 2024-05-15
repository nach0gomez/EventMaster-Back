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
    Route::post('add_new_person', 'PersonController@addNewPerson'); //metodo principal de registro de nueva persona
    Route::put('edit_person', 'PersonController@editPerson');
    Route::post('delete_person', 'PersonController@deletePerson');
    Route::get('get_all_persons', 'PersonController@getAllPersons');
    Route::get('get_person_by_id', 'PersonController@getPersonById');
    Route::post('add_new_user', 'UserController@addNewUser');
    Route::put('edit_user', 'UserController@editUser');
    Route::delete('delete_user', 'UserController@deleteUser');
    Route::get('get_all_users', 'UserController@getAllUsers');
    Route::get('get_user_by_id', 'UserController@getUserById');

