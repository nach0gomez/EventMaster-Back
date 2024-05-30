<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Router;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\AttendeeController;
use App\Http\Controllers\Api\AuthController;


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

    // //rutas de la jwt, de otra manera de poner autentificaciones
    // Route::group([

    //     'middleware' => 'api',
    //     'prefix' => 'auth'
    
    // ], function ($router) {
    
    //Route::post('login', 'AuthController@login');
    //     Route::post('logout', 'AuthController@logout');
    //     Route::post('refresh', 'AuthController@refresh');
    //     Route::post('me', 'AuthController@me');
    //     Route::post('add_new_user', 'UserController@addNewUser');
    
    // });

    Route::post('register', [AuthController::class, 'register']);   
    Route::post('login', [AuthController::class, 'login']);  //metodo de login
    Route::get('test', 'TestController@test');
    Route::post('refresh', [AuthController::class, 'refresh']);

    //person
    Route::post('add_new_person', 'PersonController@addNewPerson'); 
    //user
    Route::post('add_new_user', 'UserController@addNewUser'); //metodo principal de registro de un usuario

    //autentificacion de usuario para cada solicitud entrante al backend
    Route::middleware(['auth:sanctum'])->group(function () {

    //cerrar sesion
    Route::post('logout', [AuthController::class, 'logout']);
   
    //person
    Route::put('edit_person', 'PersonController@editPerson');
    Route::post('delete_person', 'PersonController@deletePerson');
    Route::get('get_all_persons', 'PersonController@getAllPersons');
    Route::get('get_person_by_id', 'PersonController@getPersonById');
    //user
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
    Route::get('get_events_filter', 'EventController@getEventsFilter');
    Route::get('get_events_filter_by_user', 'EventController@getEventsFilterByUser');
    Route::get('get_event_with_attendees', 'EventController@getEventWithAttendees');
    //attendee
    Route::post('add_new_attendee', 'AttendeeController@addNewAttendee');
    Route::put('edit_attendee', 'AttendeeController@editAttendee');
    Route::delete('delete_attendee', 'AttendeeController@deleteAttendee');
    Route::get('get_all_attendees', 'AttendeeController@getAllAttendees');
    Route::get('get_attendee_by_id', 'AttendeeController@getAttendeeById');
    Route::get('get_attendees_by_event_id', 'AttendeeController@getAttendeesByEventId');
    Route::get('delete_attendee', 'AttendeeController@deleteAttendee');
});


//attendee
Route::post('add_new_attendee', 'AttendeeController@addNewAttendee');
Route::put('edit_attendee', 'AttendeeController@editAttendee');
Route::delete('delete_attendee', 'AttendeeController@deleteAttendee');
Route::get('get_all_attendees', 'AttendeeController@getAllAttendees');
Route::get('get_attendee_by_id', 'AttendeeController@getAttendeeById');
Route::get('get_attendees_by_event_id', 'AttendeeController@getAttendeesByEventId');
Route::get('delete_attendee', 'AttendeeController@deleteAttendee');
