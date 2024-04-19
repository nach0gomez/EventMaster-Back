<?php

use Illuminate\Http\Request;

use App\Http\Controllers\User;
use App\Http\Controllers\Modules;
use App\Http\Controllers\Profiles;
use App\Http\Controllers\Options;
use App\Http\Controllers\Users_Options;
use App\Http\Controllers\Persons;
use App\Http\Controllers\Students;
use App\Http\Controllers\Disabilities_Students;
use App\Http\Controllers\Definitions;
use App\Http\Controllers\Companies;
use App\Http\Controllers\Tenants;
use App\Http\Controllers\Terms_and_conditions;
use App\Http\Controllers\User_terms_and_conditions;
use App\Http\Controllers\Favorite_Frequent_User_Options;
use App\Http\Controllers\ProgramsHeadquarters;
use App\Http\Controllers\User_Program_Headquarter;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Carbon\Carbon;
use App\Http\Controllers\Users_Profiles;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
/**
 * @group Auth
 *
 * APIs for managing AuthController
 */
class AuthController extends Controller {
    /**
     * Método authenticate
     * @bodyParam username string required Username, email,numero Documento para inicio de sesión
     * @bodyParam password password required Contrasena del usuario
     * @responseFile responses/Login/Login.json
     * @responseFile 422 responses/Login/ErrorLogin.json
     */
    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
        $credentials = $request->only('username', 'password');
        $id_user = null;
        if (is_numeric($request->username)) {
            $person = Persons::where('document_id', $request->username)->get();
            if ($person->count()>1 && $person->count()!=0) {
                $validator->errors()->add('username','Existen varios usuarios con el mismo número de documento, prueba con tu nombre de usuario o correo electrónico');
                return response()->json($validator->errors()->all(),422);
            } elseif($person->count()==1) {
                if ($person[0]->id_person != null) {
                    $user = User::where('id_person', $person[0]->id_person)->first();
                    if ($user) {
                        $id_user = $user->id_user;
                    }
                }
            }
        } else {
            $person = Persons::where('email', $request->username)->get();
            if ($person->count()>1 && $person->count()!=0) {
                $validator->errors()->add('username','Existen varios usuarios con el mismo correo electrónico, prueba con tu nombre de usuario o número de documento');
                return response()->json($validator->errors()->all(),422);
            } elseif ($person->count()==1){
                if ($person[0]->id_person != null) {
                    $user = User::where('id_person', $person[0]->id_person)->first();
                    if ($user) {
                        $id_user = $user->id_user;
                    }
                }
            }
        }

        try {
            if ($id_user) {
                if (!$token = JWTAuth::attempt(['id_user' => $id_user, 'password' => $request->password, 'status' => 1])) {
                    $validator->errors()->add('username','Credenciales Inválidas o usuario inactivo');
                    return response()->json($validator->errors()->all(),422);
                }
            } else {
                if (!$token = JWTAuth::attempt(['username' => $request->username, 'password' => $request->password, 'status' => 1])) {
                    $validator->errors()->add('username','Credenciales Inválidas o usuario inactivo');
                    return response()->json($validator->errors()->all(),422);
                }
            }
            
        } catch (JWTException $e) {
            return response()->json(['error' => 'No se puede crear el Token'], 500);
        }
        return AuthController::sendUserlogin($token);
    }
}