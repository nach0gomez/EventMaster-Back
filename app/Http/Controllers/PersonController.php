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
    public function index()
    {

        return Person::all();
    }

    public function index_email(Request $request)
    {
        $person = new Person;
        $person = Person::where("email", $request->email)->get()->first();
        return $person;
    }

    public function store(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'middle_name' => 'nullable|string',
            'last_name' => 'required|string',
            'second_last_name' => 'nullable|string',
            'document' => 'required|numeric|exists:users,document|unique:persons,document',
            'email' => 'required|string|exists:users,email|unique:persons,email',
            'password' => 'required|string',
            'is_eplanner' => 'required|boolean',
            'is_eattendee' => 'required|boolean',
        ]);

        if ($validator->passes()) {

            //DB::beginTransaction();
            try {
                Person::create($request->all());
                $this->Password($request);
                return response()->json([
                    'res' => true,
                    'msg' => 'Persona creada con exito'
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
        //try {

        //DB::beginTransaction();

        // Hashear la contraseña
        $hashedPassword = Hash::make($request->password);

        // Actualizar la contraseña del usuario

        Person::where("document", $request->document)->update(['password' => $hashedPassword]);
        User::where("document", $request->document)->update(['password' => $hashedPassword]);

        //DB::commit();


        return response()->json(['res' => true], 200);

        //} catch (Exception $e) {

        //    DB::rollBack();
        //    return response()->json(['error' => $e->getMessage()], 422);
    }

    public function update(Request $request)
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

    public function delete(Request $request)
    {
        Person::where("document", $request->document)->update(["status" => false]);
        User::where("document", $request->document)->update(["status" => false]);
        return response()->json([
            'res' => true,
            'msg' => 'Persona eliminada con exito'
        ], 200);
    }
}
