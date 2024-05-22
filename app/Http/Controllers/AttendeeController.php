<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendee;
use App\Models\Event;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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

    public function deleteAttendee(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_event' => 'required|numeric|exists:events,id_event',
            'id_user' => 'required|numeric|exists:users,id_user',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        try {
            $attendee = Attendee::where('id_event', $request->id_event)
                ->where('id_user', $request->id_user)
                ->first();
            $attendee->delete();
            return response()->json([
                'res' => true,
                'msg' => 'Asistencia eliminada con exito'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'res' => false,
                'msg' => 'Hubo un error inesperado en la eliminacion de la asistencia'
            ], 400);
        }
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
            'id_user' => 'required|numeric|exists:users,id_user',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Check if the combination of id_event and id_user already exists
        $existingAttendee = Attendee::where('id_event', $request->id_event)
            ->where('id_user', $request->id_user)
            ->first();

        if ($existingAttendee) {
            return response()->json(['error' => 'El usuario ya esta registrado en este evento'], 400);
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



    public function getAttendeesByEventId(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id_event' => 'required|numeric|exists:events,id_event'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'res' => false,
                'msg' => 'No se encontraron asistentes para el evento enviado'
            ], 400);
        }

        try {
            $attendee = new Attendee;
            $id_event = $request->only('id_event');
            $attendee = Attendee::where('id_event', $id_event)->get();



            return response()->json([
                'res' => true,
                'data' => $attendee
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'res' => false,
                'msg' => 'Hubo un error inesperado en la busqueda de la informacion'
            ], 400);
        }
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
}
