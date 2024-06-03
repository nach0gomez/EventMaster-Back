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
        //este middleware permite que solo los usuarios autenticados puedan acceder a los metodos del controlador
        $this->middleware('auth:sanctum')->except('addNewPerson','Password');
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
            'id_person' => 'required|numeric',
            'first_name' => 'required|string|max:30',
            'middle_name' => 'nullable|string|max:30',
            'last_name' => 'required|string|max:30',
            'second_last_name' => 'nullable|string|max:30',
            //de esta manera podemos validar que el documento, el correo y el nombre de usuario no se repitan sin tener en cuenta el usuario que se esta editando
            //y logrando asi, que si no se cambia el documento, el correo o el nombre de usuario, no se genere un error de duplicidad
            'document' => [
                'required',
                'numeric',
                Rule::unique('users', 'document')->ignore($request->id_user, 'id_user'),
            ],
            'username' => [
                'required',
                'string',
                'max:25',
                Rule::unique('users', 'username')->ignore($request->id_user, 'id_user'),
            ],
            'email' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'email')->ignore($request->id_user, 'id_user'),
            ],
            'is_eplanner' => 'required|boolean',
            'is_eattendee' => 'required|boolean',
        ], [
            'username.unique' => 'El nombre de usuario ya esta en uso.',
            'document.unique' => 'El documento ya esta registrado en nuestra base de datos.',
            'email.unique' => 'El correo electronico ya esta registrado en nuestra base de datos.',
        ]);
        if ($validator->passes()) {
            //DB::beginTransaction();
            try {
                $person = Person::findOrFail($request->id_person);
                $person->first_name = $request->first_name;
                $person->middle_name = $request->middle_name;
                $person->last_name = $request->last_name;
                $person->second_last_name = $request->second_last_name;
                $person->username = $request->username;
                $person->document = $request->document;
                $person->email = $request->email;
                $person->is_eplanner = $request->is_eplanner;
                $person->is_eattendee = $request->is_eattendee;
                $person->status = true;
                $person->save();

                return response()->json([
                    'msg' => 'Persona actualizada con exito'
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
