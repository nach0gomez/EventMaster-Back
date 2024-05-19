<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Models\Person;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{


    public function authenticate(Request $request)
    {
        // Validar los datos de entrada
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        //validar si el usuario esta activo
        $existsUser = User::where("email",$request->email)->where("status",true)->get()->first();

        if(!$existsUser){
            return response()->json(['error' => 'Usuario no encontrado'], 401);
        }

        // Intentar autenticar al usuario
        $credentials = $request->only('email', 'password');
        if (!Auth::attempt($credentials)) {
            return response()->json(['error' => 'Credenciales invalidas'], 401);
        }

        // Si las credenciales son válidas, obtener el usuario
        $user = Auth::user();

        // Generar el token JWT
        $token = $user->createToken('TokenName')->accessToken;

        // Retornar el token JWT en la respuesta
        return response()->json(['token' => $token,
                                 'user'=>$existsUser]);
    }



    public function register(Request $request)
    {
        // Validar los datos de entrada

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:users',
            'document' => 'required|string2|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        //caso de error en la validacion
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 422);
        }

        // Crear el nuevo usuario
        $user = new User([
            'username' => $request->username,
            'document' => $request->document,
            'password' => Hash::make($request->password),
            'status' => true,
        ]);

        // Guardar el usuario en la base de datos
        $user->save();

        // Retornar una respuesta de éxito
        return response()->json(['message' => 'Usuario registrado exitosamente'], 201);
    }
}
