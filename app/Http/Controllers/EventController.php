<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\Event;
use App\Models\Attendee;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller

{

    public function __construct()
    {
        //este middleware permite que solo los usuarios autenticados puedan acceder a los metodos del controlador
        $this->middleware('auth:sanctum');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addNewEvent(Request $request)
    {

        $validator = Validator::make($request->all(), [

            'title' => 'required|string',
            'description' => 'required|string',
            'date' => 'required|string',
            'time' => 'required|string',
            'location' => 'required|string',
            'duration' => 'required|numeric',
            'status' => 'required|string',
            'event_type' => 'required|string',
            'id_user' => 'required|numeric|exists:users,id_user',
            'restriction_minors_allowed' => 'required|boolean',
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
                ], 422);
            }
        }
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 422);
        }
    }

    public function getEventWithAttendees(Request $request)
    {
        try {
            $event = Event::with('attendees')->find($request->id_event);
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
    //Filtrar eventos por fecha, tipo de evento, título y ubicación
    public function getEventsFilter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'date' => 'nullable|string',
            'location' => 'nullable|string',
            'event_type' => 'nullable|string',
        ]);
        $validator2 = Validator::make($request->all(), [
            'title' => 'nullable|string',
            'date' => 'required|string',
            'location' => 'nullable|string',
            'event_type' => 'nullable|string',
        ]);
        $validator3 = Validator::make($request->all(), [
            'title' => 'nullable|string',
            'date' => 'nullable|string',
            'location' => 'required|string',
            'event_type' => 'nullable|string',
        ]);
        $validator4 = Validator::make($request->all(), [
            'title' => 'nullable|string',
            'date' => 'nullable|string',
            'location' => 'nullable|string',
            'event_type' => 'required|string',
        ]);
        if ($validator->fails() && $validator2->fails() && $validator3->fails() && $validator4->fails()) {
            return response()->json([
                'res' => true,
                'data' => $events = Event::with('attendees')->where('status', 1)->get()
            ]);
        } else {
            try {
                $query = Event::with('attendees');

                // Filtrar por tipo de evento si se proporciona
                if ($request->has('event_type') && $request->event_type != null) { // si tiene categoria
                    if ($request->has('date') && $request->date != null) { // si tiene fecha
                        if ($request->has('title') && $request->title != null) { // si tiene título
                            if ($request->has('location') && $request->location != null) { // si tiene location
                                $query->where('title', 'like', '%' . $request->title . '%')
                                    ->where('date', '=', $request->date)
                                    ->where('event_type', '=', $request->event_type)
                                    ->where('location', 'like', '%' . $request->location . '%');
                                $events = $query->get();
                            } else {
                                $query->where('title', 'like', '%' . $request->title . '%')
                                    ->where('date', '=', $request->date)
                                    ->where('event_type', '=', $request->event_type);
                                $events = $query->get();
                            }
                        } else { // si no tiene título pero si categoria y fecha
                            if ($request->has('location') && $request->location != null) { // si tiene location
                                $query->where('date', '=', $request->date)
                                    ->where('event_type', '=', $request->event_type)
                                    ->where('location', 'like', '%' . $request->location . '%');
                                $events = $query->get();
                            } else {
                                $query->where('date', '=', $request->date)
                                    ->where('event_type', '=', $request->event_type);
                                $events = $query->get();
                            }
                        }
                    } else { // si tiene categoria pero no fecha
                        if ($request->has('title') && $request->title != null) { // si tiene título y categoria
                            if ($request->has('location') && $request->location != null) { // si tiene location
                                $query->where('title', 'like', '%' . $request->title . '%')
                                    ->where('event_type', '=', $request->event_type)
                                    ->where('location', 'like', '%' . $request->location . '%');
                                $events = $query->get();
                            } else {
                                $query->where('title', 'like', '%' . $request->title . '%')
                                    ->where('event_type', '=', $request->event_type);
                                $events = $query->get();
                            }
                        } else { // si no tiene título ni fecha, pero si categoria
                            if ($request->has('location') && $request->location != null) { // si tiene location
                                $query->where('event_type', '=', $request->event_type)
                                    ->where('location', 'like', '%' . $request->location . '%');
                                $events = $query->get();
                            } else {
                                $query->where('event_type', '=', $request->event_type);
                                $events = $query->get();
                            }
                        }
                    }
                } else { // si no tiene categoria
                    if ($request->has('date') && $request->date != null) { // si tiene fecha
                        if ($request->has('title') && $request->title != null) { // si tiene título
                            if ($request->has('location') && $request->location != null) { // si tiene location
                                $query->where('title', 'like', '%' . $request->title . '%')
                                    ->where('date', '=', $request->date)
                                    ->where('location', 'like', '%' . $request->location . '%');
                                $events = $query->get();
                            } else {
                                $query->where('title', 'like', '%' . $request->title . '%')
                                    ->where('date', '=', $request->date);
                                $events = $query->get();
                            }
                        } else { // si no tiene título ni categoria pero si fecha
                            if ($request->has('location') && $request->location != null) { // si tiene location
                                $query->where('date', '=', $request->date)
                                    ->where('location', 'like', '%' . $request->location . '%');
                                $events = $query->get();
                            } else {
                                $query->where('date', '=', $request->date);
                                $events = $query->get();
                            }
                        }
                    } else { // si no tiene categoria ni fecha
                        if ($request->has('title') && $request->title != null) { // si tiene título
                            if ($request->has('location') && $request->location != null) { // si tiene location
                                $query->where('title', 'like', '%' . $request->title . '%')
                                    ->where('location', 'like', '%' . $request->location . '%');
                                $events = $query->get();
                            } else {
                                $query->where('title', 'like', '%' . $request->title . '%');
                                $events = $query->get();
                            }
                        } else { // si no tiene nada
                            if ($request->has('location') && $request->location != null) { // si tiene location
                                $query->where('location', 'like', '%' . $request->location . '%');
                                $events = $query->get();
                            } else {
                                if (!$request->has('event_type') && !$request->has('date') && !$request->has('title') && !$request->has('location')) {
                                    return response()->json([
                                        'res' => false,
                                        'msg' => 'No se proporcionaron filtros para la consulta'
                                    ]);
                                }
                            }
                        }
                    }
                }

                // Obtener los resultados de la consulta
                if ($events->isEmpty()) {
                    return response()->json([
                        'res' => false,
                        'msg' => 'No se encontraron eventos con esas características'
                    ]);
                }

                return response()->json([
                    'res' => 'true',
                    'data' => $events
                ]);
            } catch (Exception $e) {
                return response()->json([
                    'res' => false,
                    'msg' => $e->getMessage()
                ]);
            }
        }
    }


    //filtra eventos por usuario
    public function getEventsFilterByUser(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'title' => 'required|string',
            'date' => 'nullable|string',
            'location' => 'nullable|string',
            'event_type' => 'nullable|string',
            'id_user' => 'required|string|exists:users,id_user',
        ]);
        $validator2 = Validator::make($request->all(), [

            'title' => 'nullable|string',
            'date' => 'required|string',
            'location' => 'nullable|string',
            'event_type' => 'nullable|string',
            'id_user' => 'required|string|exists:users,id_user',
        ]);
        $validator3 = Validator::make($request->all(), [

            'title' => 'nullable|string',
            'date' => 'nullable|string',
            'location' => 'required|string',
            'event_type' => 'nullable|string',
            'id_user' => 'required|string|exists:users,id_user',
        ]);
        $validator4 = Validator::make($request->all(), [

            'title' => 'nullable|string',
            'date' => 'nullable|string',
            'location' => 'nullable|string',
            'event_type' => 'required|string',
            'id_user' => 'required|string|exists:users,id_user',
        ]);
        $validator5 = Validator::make($request->all(), [

            'title' => 'nullable|string',
            'date' => 'nullable|string',
            'location' => 'nullable|string',
            'event_type' => 'nullable|string',
            'id_user' => 'required|numeric|exists:users,id_user',
        ]);
        if ($validator->fails() && $validator2->fails() && $validator3->fails() && $validator5->passes() && $validator4->fails()) {

            $events = Event::all()->where('status', 1)
                ->where('id_user', '=', $request->id_user);

            return response()->json([
                'res' => true,
                'data' => $events->values()
            ]);
        } else {
            try {
                $query = Event::query();

                // Filtrar por tipo de evento si se proporciona
                if ($request->has('event_type') && $request->event_type != null) { // si tiene categoria
                    if ($request->has('date') && $request->date != null) { // si tiene fecha
                        if ($request->has('title') && $request->title != null) { // si tiene título
                            if ($request->has('location') && $request->location != null) { // si tiene location
                                if ($request->has('id_user') && $request->id_user != null) { // si tiene id_user
                                    $query->where('title', 'like', '%' . $request->title . '%')
                                        ->where('date', '=', $request->date)
                                        ->where('event_type', '=', $request->event_type)
                                        ->where('location', 'like', '%' . $request->location . '%')
                                        ->where('id_user', '=', $request->id_user);
                                    $events = $query->get();
                                }
                            } else {
                                if ($request->has('id_user') && $request->id_user != null) { // si tiene id_user
                                    $query->where('title', 'like', '%' . $request->title . '%')
                                        ->where('date', '=', $request->date)
                                        ->where('event_type', '=', $request->event_type)
                                        ->where('id_user', '=', $request->id_user);
                                    $events = $query->get();
                                }
                            }
                        } else { // si no tiene título pero si categoria y fecha
                            if ($request->has('location') && $request->location != null) { // si tiene location
                                if ($request->has('id_user') && $request->id_user != null) { // si tiene id_user
                                    $query->where('date', '=', $request->date)
                                        ->where('event_type', '=', $request->event_type)
                                        ->where('location', 'like', '%' . $request->location . '%')
                                        ->where('id_user', '=', $request->id_user);
                                    $events = $query->get();
                                }
                            } else {
                                if ($request->has('id_user') && $request->id_user != null) { // si tiene id_user
                                    $query->where('date', '=', $request->date)
                                        ->where('event_type', '=', $request->event_type)
                                        ->where('id_user', '=', $request->id_user);
                                    $events = $query->get();
                                }
                            }
                        }
                    } else { // si tiene categoria pero no fecha
                        if ($request->has('title') && $request->title != null) { // si tiene título y categoria
                            if ($request->has('location') && $request->location != null) { // si tiene location
                                if ($request->has('id_user') && $request->id_user != null) { // si tiene id_user
                                    $query->where('title', 'like', '%' . $request->title . '%')
                                        ->where('event_type', '=', $request->event_type)
                                        ->where('location', 'like', '%' . $request->location . '%')
                                        ->where('id_user', '=', $request->id_user);
                                    $events = $query->get();
                                }
                            } else {
                                if ($request->has('id_user') && $request->id_user != null) { // si tiene id_user
                                    $query->where('title', 'like', '%' . $request->title . '%')
                                        ->where('event_type', '=', $request->event_type)
                                        ->where('id_user', '=', $request->id_user);
                                    $events = $query->get();
                                }
                            }
                        } else { // si no tiene título ni fecha, pero si categoria
                            if ($request->has('location') && $request->location != null) { // si tiene location
                                if ($request->has('id_user') && $request->id_user != null) { // si tiene id_user
                                    $query->where('event_type', '=', $request->event_type)
                                        ->where('location', 'like', '%' . $request->location . '%')
                                        ->where('id_user', '=', $request->id_user);
                                    $events = $query->get();
                                }
                            } else {
                                if ($request->has('id_user') && $request->id_user != null) { // si tiene id_user
                                    $query->where('event_type', '=', $request->event_type)
                                        ->where('id_user', '=', $request->id_user);
                                    $events = $query->get();
                                }
                            }
                        }
                    }
                } else { // si no tiene categoria
                    if ($request->has('date') && $request->date != null) { // si tiene fecha
                        if ($request->has('title') && $request->title != null) { // si tiene título
                            if ($request->has('location') && $request->location != null) { // si tiene location
                                if ($request->has('id_user') && $request->id_user != null) { // si tiene id_user
                                    $query->where('title', 'like', '%' . $request->title . '%')
                                        ->where('date', '=', $request->date)
                                        ->where('location', 'like', '%' . $request->location . '%')
                                        ->where('id_user', '=', $request->id_user);
                                    $events = $query->get();
                                }
                            } else {
                                if ($request->has('id_user') && $request->id_user != null) { // si tiene id_user
                                    $query->where('title', 'like', '%' . $request->title . '%')
                                        ->where('date', '=', $request->date)
                                        ->where('id_user', '=', $request->id_user);
                                    $events = $query->get();
                                }
                            }
                        } else { // si no tiene título ni categoria pero si fecha
                            if ($request->has('location') && $request->location != null) { // si tiene location
                                if ($request->has('id_user') && $request->id_user != null) { // si tiene id_user
                                    $query->where('date', '=', $request->date)
                                        ->where('location', 'like', '%' . $request->location . '%')
                                        ->where('id_user', '=', $request->id_user);
                                    $events = $query->get();
                                }
                            } else {
                                if ($request->has('id_user') && $request->id_user != null) { // si tiene id_user
                                    $query->where('date', '=', $request->date)
                                        ->where('id_user', '=', $request->id_user);
                                    $events = $query->get();
                                }
                            }
                        }
                    } else { // si no tiene categoria ni fecha
                        if ($request->has('title') && $request->title != null) { // si tiene título
                            if ($request->has('location') && $request->location != null) { // si tiene location
                                if ($request->has('id_user') && $request->id_user != null) { // si tiene id_user
                                    $query->where('title', 'like', '%' . $request->title . '%')
                                        ->where('location', 'like', '%' . $request->location . '%')
                                        ->where('id_user', '=', $request->id_user);
                                    $events = $query->get();
                                }
                            } else {
                                if ($request->has('id_user') && $request->id_user != null) { // si tiene id_user
                                    $query->where('title', 'like', '%' . $request->title . '%')
                                        ->where('id_user', '=', $request->id_user);
                                    $events = $query->get();
                                }
                            }
                        } else { // si no tiene nada
                            if ($request->has('location') && $request->location != null) { // si tiene location
                                if ($request->has('id_user') && $request->id_user != null) { // si tiene id_user
                                    $query->where('location', 'like', '%' . $request->location . '%')
                                        ->where('id_user', '=', $request->id_user);
                                    $events = $query->get();
                                }
                            } else {
                                if ($request->has('id_user') && $request->id_user != null) { // si tiene id_user
                                    $query->where('id_user', '=', $request->id_user);
                                    $events = $query->get();
                                } else {
                                    if (!$request->has('event_type') && !$request->has('date') && !$request->has('title') && !$request->has('location') && !$request->has('id_user')) {
                                        return response()->json([
                                            'res' => false,
                                            'msg' => 'No se proporcionaron filtros para la consulta'
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }
                if ($request->has('event_type') && $request->event_type == null) { // si tiene categoria null
                    if ($request->has('date') && $request->date == null) { // si tiene fecha null
                        if ($request->has('title') && $request->title == null) { // si tiene título null
                            if ($request->has('location') && $request->location == null) { // si tiene location null
                                if ($request->has('id_user') && $request->id_user != null) { // si tiene id_user
                                    $events = Event::all()->where('status', 1)
                                        ->where('id_user', '=', $request->id_user);
                                    // Obtener los resultados de la consulta
                                    $events = $events->with('attendees')->get();
                                }
                            }
                        }
                    }
                }


                if ($events->isEmpty()) {
                    return response()->json([
                        'res' => false,
                        'msg' => 'No se encontraron eventos con esas características'
                    ]);
                }

                return response()->json([
                    'res' => 'true',
                    'data' => $events
                ]);
            } catch (Exception $e) {
                return response()->json([
                    'res' => false,
                    'msg' => $e->getMessage()
                ]);
            }
        }
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editEvent(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'id_event' => 'required|numeric|exists:events,id_event',
            'title' => 'required|string',
            'description' => 'required|string',
            'date' => 'required|string',
            'time' => 'required|string',
            'location' => 'required|string',
            'duration' => 'required|numeric',
            'status' => 'required|numeric',
            'event_type' => 'required|string',
            'id_user' => 'required|numeric|exists:users,id_user',
            'restriction_minors_allowed' => 'required|boolean',
            'max_attendees' => 'required|numeric'
        ]);

        if ($validator->passes()) {

            //DB::beginTransaction();
            try {
                // es mejor para manejar cada dato, mandar los datos de la request uno por uno
                $event = Event::findOrFail($request->id_event);
                $event->title = $request->title;
                $event->description = $request->description;
                $event->date = $request->date;
                $event->time = $request->time;
                $event->location = $request->location;
                $event->duration = $request->duration;
                $event->status = $request->status;
                $event->id_user = $request->id_user;
                $event->event_type = $request->event_type;
                $event->restriction_minors_allowed = $request->restriction_minors_allowed;
                $event->max_attendees = $request->max_attendees;
                $event->save(); //guardamos en la bd


                return response()->json([
                    'res' => true,
                    'msg' => 'Evento actualizado con exito'
                ]);
                //    DB::commit();
            } catch (Exception $e) {
                //DB::rollback();
                return response()->json([
                    'res' => false,
                    'msg' => $e->getMessage(), 422
                ]);
            }
        }
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteEvent(Request $request)
{
    // Iniciar una transacción
    $validator = Validator::make($request->all(), [

        'id_event' => 'required|numeric|exists:events,id_event'
    ]);

   // DB::beginTransaction();
   if ($validator->passes()) {
    try {
        // Encontrar el evento
        //aqui se detiene
        $event = Event::findOrFail($request->id_event);

        // Eliminar todas las asistencias relacionadas con el evento
        Attendee::where('id_event', $request->id_event)->delete();

        // Eliminar el evento
        $event->delete();

        // Confirmar la transacción
        //DB::commit();

        return response()->json([
            'res' => true,
            'msg' => 'Evento y sus asistencias relacionadas eliminados con éxito'
        ], Response::HTTP_OK);

    } catch (Exception $e) {
        // Revertir la transacción en caso de error
       // DB::rollback();
    }
    }
    if ($validator->fails()) {
        return response()->json($validator->errors()->all(), 422);
    }
            return response()->json([
                'res' => false,
                'msg' => 'Error al eliminar el evento y sus asistencias relacionadas'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        
}
}
