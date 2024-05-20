<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\Event;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class EventController extends Controller
{


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addNewEvent(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'title'=> 'required|string',
            'description'=> 'required|string',
            'date'=> 'required|string',
            'time'=> 'required|string',
            'location'=> 'required|string',
            'duration'=> 'required|numeric',
            'status'=> 'required|string',
            'event_type'=> 'required|string',
            'id_user' => 'required|numeric|exists:users,id_user',
            'restriction_minors_allowed'=> 'required|boolean',
            'max_attendees' => 'required|numeric'
        ]);

        if ($validator->passes()) {

            //DB::beginTransaction();
            try {
                // es mejor para manejar cada dato, mandar los datos de la request uno por uno
                $event = new Event;
                $event->title = $request->title;
                $event->description = $request->description;
                $event->date = $request->date;
                $event->time = $request->time;
                $event->location = $request->location;
                $event->duration = $request->duration;
                $event->status = true; //por defecto guarda como true
                $event->id_user = $request->id_user;
                $event->event_type = $request->event_type;
                $event->restriction_minors_allowed = $request->restriction_minors_allowed;
                $event->max_attendees = $request->max_attendees;
                $event->save(); //guardamos en la bd


                return response()->json([
                    'res' => true,
                    'msg' => 'Evento registrado con exito'
                ]);
                //    DB::commit();
            } catch (Exception $e) {
                //DB::rollback();
                return response()->json([
                    'res' => false,
                    'msg' => $e->getMessage()
                ]);
            }
        }
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 422);
        }
    }
    

    
    public function getAllEvents()
    {
        //retornamos todos los eventos suponeindo que el estado activado es 1
        return Event::all()->where('status', 1);
    }

    /**
     * Display the specified event depending on the ID provided.
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getEventById(Request $request)
    {
        try {
            $event = new Event;
            $id_event = $request->only('id_event');
            $event = Event::findOrFail($id_event);
            return response()->json([
                'res' => 'true',
                'data' => $event
            ]);
        } catch (Exception $e) {
            return response()->json([
                'res' => false,
                'msg' => $e->getMessage()
            ]);
        }

    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editEvent(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteEvent($id)
    {
        //
    }
}
