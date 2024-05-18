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
    public function index()
    {
        return Attendee::all();
    }

    public function indexId(Request $request)
    {
        $attendee = new Attendee;
        $id_attendee = $request->only('id_attendee');
        $attendee = Attendee::findOrFail($id_attendee);
        return $attendee;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
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
