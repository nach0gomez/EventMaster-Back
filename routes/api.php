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
    Route::post('login', 'AuthController@authenticate');  //metodo de login
    Route::get('test', 'TestController@test');
    //person
    Route::post('add_new_person', 'PersonController@addNewPerson'); 
    Route::put('edit_person', 'PersonController@editPerson');
    Route::post('delete_person', 'PersonController@deletePerson');
    Route::get('get_all_persons', 'PersonController@getAllPersons');
    Route::get('get_person_by_id', 'PersonController@getPersonById');
    //user
    Route::post('add_new_user', 'UserController@addNewUser'); //metodo principal de registro de un usuario
    Route::put('edit_user', 'UserController@editUser');
    Route::post('delete_user', 'UserController@deleteUser');
    Route::get('get_all_users', 'UserController@getAllUsers');
    Route::get('get_user_by_id', 'UserController@getUserById');
    //event
    Route::post('add_new_event', 'EventController@addNewEvent');
    Route::put('edit_event', 'EventController@editEvent');
    Route::delete('delete_event', 'EventController@deleteEvent');
    Route::get('get_all_events', 'EventController@getAllEvents');
    Route::get('get_event_by_id', 'EventController@getEventById');
    //attendee
    Route::post('add_new_attendee', 'AttendeeController@addNewAttendee');
    Route::put('edit_attendee', 'AttendeeController@editAttendee');
    Route::delete('delete_attendee', 'AttendeeController@deleteAttendee');
    Route::get('get_all_attendees', 'AttendeeController@getAllAttendees');
    Route::get('get_attendee_by_id', 'AttendeeController@getAttendeeById');


