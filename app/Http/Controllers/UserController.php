<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use JWTAuth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Person;
use Exception;

//use Mail;
//use Mail\Create_User_Mail;
//use Mail\NotificateRecuperacionContraseña;

/**
 * @group Users
 * 
 * APIs for managing Users_Controller 
 */
class UserController extends Controller
{
    public function __construct()
    {
    }

    /**
     * Método index
     * @authenticated
     * @responseFile responses/Users/UsersIndex.json
     *
     */
    public function getAllUsers()
    {

        return User::all();
    }

    public function getUserById(Request $request)
    {
        try {
            $user = new User;
            $document = $request->only('id_user');
            $user = User::findOrFail($document);
            return response()->json(['data' => $user], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Método store
     * @authenticated
     * @bodyParam document numeric required identidicador unico de existencia de usuario.
     * @bodyParam username string Nombre usuario 
     * @bodyParam password string required Contraseña del usuario
     * @bodyParam profiles array required
     * @bodyParam profiles.* numeric required indica una o muchas opciones que tiene
     * @responseFile responses/Users/UsersStore.json
     * @responseFile 422 responses/ErrorGeneral/ErrorGeneral1.json
     * @responseFile 402 responses/ErrorGeneral/ErrorGeneral2.json
     * @responseFile 403 responses/ErrorGeneral/ErrorGeneral3.json
     * @responseFile 404 responses/ErrorGeneral/ErrorGeneral4.json
     */
    public function addNewUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:30',
            'middle_name' => 'nullable|string|max:30',
            'last_name' => 'required|string|max:30',
            'second_last_name' => 'nullable|string|max:30',
            'username' => 'required|string|max:25|unique:users,username',
            'document' => 'required|numeric|unique:users,document',
            'email' => 'required|string|max:50|unique:users,email',
            'password' => 'required|string|max:20',
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
                $person = new User;
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


    public function editUser(Request $request, $id)
    {
        if ($id != $request->id_user) {
            return response()->json(['errors' => array(['code' => 401, 'message' => 'No se suministran los parámetros mínimos de búsqueda.'])], 401);
        } else {
            $user = new User;
            $user = User::findOrFail($id);
            $user->email = $request->email;
            $user->username = $request->username;
            $user->document = $request->document;
            $user->password = Hash::make($request->password);
            $user->status = true;
            // Guardamos el cambio en nuestro modelo
            $user->save();
        }
    }

    public function deleteUser(Request $request)
    {
        Person::where("document", $request->document)->update(["status" => false]);
        User::where("document", $request->document)->update(["status" => false]);
        return response()->json([
            'res' => true,
            'msg' => 'Persona eliminada con exito'
        ], 200);
    }

    /*cambio de contraseña*/
    public function generateRandomPassword($length = 8)
    {
        $characters = '0123456789abcd';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    public function updatePassword(Request $request)
    {

        // Se agrega la validación
        $validator = Validator::make($request->all(), [
            'document' => 'required|exists:users,document',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 422);
        }
        try {

            DB::beginTransaction();

            // Generar contraseña aleatoria
            $newPassword = $this->generateRandomPassword();

            // Hashear la nueva contraseña
            $hashedPassword = Hash::make($newPassword);

            // Actualizar la contraseña del usuario

            User::where("document", $request->document)->update(['password' => $hashedPassword]);
            Person::where("document", $request->document)->update(['password' => $hashedPassword]);

            DB::commit();


            return response()->json(['message' => 'la contraseña se actualizo con exito'], 200);
        } catch (Exception $e) {

            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
