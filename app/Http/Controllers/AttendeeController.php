<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendee;
use App\Models\Event;

class AttendeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAllAttendees()
    {
        return Attendee::all();
    }

    public function getAttendeeById(Request $request)
    {
        $attendee = new Attendee;
        $id_attendee = $request->only('id_attendee');
        $attendee = Attendee::findOrFail($id_attendee);
        return $attendee;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addNewAttendee(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'id_event' => 'required|numeric|exists:events,id_event',
            'id_user' => 'required|numeric|exists:users,id_user'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        $attendee = new Attendee;
        $attendee->id_event = $request->id_event;
        $attendee->id_user = $request->id_user;
        $attendee->save();
        return response()->json([
            'res' => true,
            'msg' => 'Asistencia registrado con exito'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editAttendee(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteAttendee(Request $request)
    {
        $attendee = new Attendee;
        $id_attendee = $request->only('id_attendee');
        $attendee = Attendee::findOrFail($id_attendee);
        $attendee->delete();
        return response()->json([
            'res' => true,
            'msg' => 'Asistencia eliminada con exito'
        ]);
    }
    
}
