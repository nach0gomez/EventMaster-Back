<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use JWTAuth;


class PersonController extends Controller
{

    public function __construct()
    {
    }

    /**
     * Método index
     * @authenticated
     * @responseFile responses/Persons/PersonsIndex.json
     */
    public function getAllPersons()
    {

        return Person::all();
    }

    public function getPersonById(Request $request)
    {
        $person = new Person;
        $person = Person::where("email", $request->email)->get()->first();
        return $person;
    }

    public function addNewPerson(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:30',
            'middle_name' => 'nullable|string|max:30',
            'last_name' => 'required|string|max:30',
            'second_last_name' => 'nullable|string|max:30',
            'username' => 'required|string|max:25|unique:persons,username',
            'document' => 'required|numeric|unique:persons,document',
            'email' => 'required|string|max:70|unique:persons,email',
            'password' => 'required|string|max:40',
            'is_eplanner' => 'required|boolean',
            'is_eattendee' => 'required|boolean',
        ]);

        if ($validator->passes()) {

            //DB::beginTransaction();
            try {
                $person = new Person;
                $person->first_name = $request->first_name;
                $person->middle_name = $request->middle_name;
                $person->last_name = $request->last_name;
                $person->second_last_name = $request->second_last_name;
                $person->username = $request->username;
                $person->document = $request->document;
                $person->email = $request->email;
                $person->password = Hash::make($request->password);
                $person->is_eplanner = $request->is_eplanner;
                $person->is_eattendee = $request->is_eattendee;
                $person->status = true;
                $person->save();

                return response()->json([
                    'msg' => 'Persona creada con exito'
                ], 200);
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

    //funcion para hashear lsa contraseñas, tanto en usurios como en personas
    public function Password(Request $request)
    {

        // Se agrega la validación
        $validator = Validator::make($request->all(), [
            'document' => 'required|exists:persons,document'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 422);
        }
        try {

            //DB::beginTransaction();

            // Hashear la contraseña
            $hashedPassword = Hash::make($request->password);

            // Actualizar la contraseña del usuario

            Person::where("document", $request->document)->update(['password' => $hashedPassword]);

            //DB::commit();


            return response()->json(['res' => true], 200);
        } catch (Exception $e) {

            //    DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function editPerson(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'middle_name' => 'nullable|string',
            'last_name' => 'required|string',
            'second_last_name' => 'nullable|string',
            'document' => 'required|numeric|exists:users,document|exists:persons,document',
            'email' => 'required|string',
            'username' => 'required|string',
            'status' => 'required|boolean',
            'is_admin' => 'nullable|boolean',
            'is_eplanner' => 'required|boolean',
            'is_eattendee' => 'required|boolean',
        ]);
        if ($validator->passes()) {
            Person::where("document", $request->document)->update($request->all());
            User::where("id_documentuser", $request->document)->update($request->only('email', 'document', 'username', "status"));

            return response()->json([
                'res' => true,
                'msg' => 'Persona editada con exito'
            ], 200);
        }
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 422);
        }
    }

    public function deletePerson(Request $request)
    {
        Person::where("document", $request->document)->update(["status" => false]);
        User::where("document", $request->document)->update(["status" => false]);
        return response()->json([
            'res' => true,
            'msg' => 'Persona eliminada con exito'
        ], 200);
    }
}
