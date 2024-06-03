<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendee;
use App\Models\Event;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AttendeeController extends Controller
{

    public function __construct()
    {
        //este middleware permite que solo los usuarios autenticados puedan acceder a los metodos del controlador
        $this->middleware('auth:sanctum');
    }
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



    public function getAttendeesFilterByUser(Request $request)
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
        $events = Event::where('status', 1)
            ->whereHas('attendees', function ($query) use ($request) {
                $query->where('attendees.id_user', $request->id_user);
            })->get();

        return response()->json([
            'res' => true,
            'data' => $events
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
                                    ->whereHas('attendees', function ($q) use ($request) {
                                        $q->where('attendees.id_user', $request->id_user);
                                    });
                                $events = $query->get();
                            }
                        } else {
                            if ($request->has('id_user') && $request->id_user != null) { // si tiene id_user
                                $query->where('title', 'like', '%' . $request->title . '%')
                                    ->where('date', '=', $request->date)
                                    ->where('event_type', '=', $request->event_type)
                                    ->whereHas('attendees', function ($q) use ($request) {
                                        $q->where('attendees.id_user', $request->id_user);
                                    });
                                $events = $query->get();
                            }
                        }
                    } else { // si no tiene título pero si categoria y fecha
                        if ($request->has('location') && $request->location != null) { // si tiene location
                            if ($request->has('id_user') && $request->id_user != null) { // si tiene id_user
                                $query->where('date', '=', $request->date)
                                    ->where('event_type', '=', $request->event_type)
                                    ->where('location', 'like', '%' . $request->location . '%')
                                    ->whereHas('attendees', function ($q) use ($request) {
                                        $q->where('attendees.id_user', $request->id_user);
                                    });
                                $events = $query->get();
                            }
                        } else {
                            if ($request->has('id_user') && $request->id_user != null) { // si tiene id_user
                                $query->where('date', '=', $request->date)
                                    ->where('event_type', '=', $request->event_type)
                                    ->whereHas('attendees', function ($q) use ($request) {
                                        $q->where('attendees.id_user', $request->id_user);
                                    });
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
                                    ->whereHas('attendees', function ($q) use ($request) {
                                        $q->where('attendees.id_user', $request->id_user);
                                    });
                                $events = $query->get();
                            }
                        } else {
                            if ($request->has('id_user') && $request->id_user != null) { // si tiene id_user
                                $query->where('title', 'like', '%' . $request->title . '%')
                                    ->where('event_type', '=', $request->event_type)
                                    ->whereHas('attendees', function ($q) use ($request) {
                                        $q->where('attendees.id_user', $request->id_user);
                                    });
                                $events = $query->get();
                            }
                        }
                    } else { // si no tiene título ni fecha, pero si categoria
                        if ($request->has('location') && $request->location != null) { // si tiene location
                            if ($request->has('id_user') && $request->id_user != null) { // si tiene id_user
                                $query->where('event_type', '=', $request->event_type)
                                    ->where('location', 'like', '%' . $request->location . '%')
                                    ->whereHas('attendees', function ($q) use ($request) {
                                        $q->where('attendees.id_user', $request->id_user);
                                    });
                                $events = $query->get();
                            }
                        } else {
                            if ($request->has('id_user') && $request->id_user != null) { // si tiene id_user
                                $query->where('event_type', '=', $request->event_type)
                                    ->whereHas('attendees', function ($q) use ($request) {
                                        $q->where('attendees.id_user', $request->id_user);
                                    });
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
                                    ->whereHas('attendees', function ($q) use ($request) {
                                        $q->where('attendees.id_user', $request->id_user);
                                    });
                                $events = $query->get();
                            }
                        } else {
                            if ($request->has('id_user') && $request->id_user != null) { // si tiene id_user
                                $query->where('title', 'like', '%' . $request->title . '%')
                                    ->where('date', '=', $request->date)
                                    ->whereHas('attendees', function ($q) use ($request) {
                                        $q->where('attendees.id_user', $request->id_user);
                                    });
                                $events = $query->get();
                            }
                        }
                    } else { // si no tiene título pero si fecha
                        if ($request->has('location') && $request->location != null) { // si tiene location
                            if ($request->has('id_user') && $request->id_user != null) { // si tiene id_user
                                $query->where('date', '=', $request->date)
                                    ->where('location', 'like', '%' . $request->location . '%')
                                    ->whereHas('attendees', function ($q) use ($request) {
                                        $q->where('attendees.id_user', $request->id_user);
                                    });
                                $events = $query->get();
                            }
                        } else {
                            if ($request->has('id_user') && $request->id_user != null) { // si tiene id_user
                                $query->where('date', '=', $request->date)
                                    ->whereHas('attendees', function ($q) use ($request) {
                                        $q->where('attendees.id_user', $request->id_user);
                                    });
                                $events = $query->get();
                            }
                        }
                    }
                } else { // si no tiene fecha ni categoria
                    if ($request->has('title') && $request->title != null) { // si tiene título
                        if ($request->has('location') && $request->location != null) { // si tiene location
                            if ($request->has('id_user') && $request->id_user != null) { // si tiene id_user
                                $query->where('title', 'like', '%' . $request->title . '%')
                                    ->where('location', 'like', '%' . $request->location . '%')
                                    ->whereHas('attendees', function ($q) use ($request) {
                                        $q->where('attendees.id_user', $request->id_user);
                                    });
                                $events = $query->get();
                            }
                        } else {
                            if ($request->has('id_user') && $request->id_user != null) { // si tiene id_user
                                $query->where('title', 'like', '%' . $request->title . '%')
                                    ->whereHas('attendees', function ($q) use ($request) {
                                        $q->where('attendees.id_user', $request->id_user);
                                    });
                                $events = $query->get();
                            }
                        }
                    } else { // si no tiene título ni fecha ni categoria
                        if ($request->has('location') && $request->location != null) { // si tiene location
                            if ($request->has('id_user') && $request->id_user != null) { // si tiene id_user
                                $query->where('location', 'like', '%' . $request->location . '%')
                                    ->whereHas('attendees', function ($q) use ($request) {
                                        $q->where('attendees.id_user', $request->id_user);
                                    });
                                $events = $query->get();
                            }
                        } else {
                            if ($request->has('id_user') && $request->id_user != null) { // si tiene id_user
                                $query->whereHas('attendees', function ($q) use ($request) {
                                    $q->where('attendees.id_user', $request->id_user);
                                });
                                $events = $query->get();
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
                'res' => true,
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
    public function editAttendee(Request $request, $id)
    {
        //
    }
}
