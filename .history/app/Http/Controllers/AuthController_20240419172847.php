<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
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

        // Intentar autenticar al usuario
        $credentials = $request->only('email', 'password');
        if (!Auth::attempt($credentials)) {
            return response()->json(['error' => 'Credenciales inválidas'], 401);
        }

        // Si las credenciales son válidas, obtener el usuario
        $user = Auth::user();

        // Generar el token JWT
        $token = $user->createToken('TokenName')->accessToken;

        // Retornar el token JWT en la respuesta
        return response()->json(['token' => $token]);
    }


    public function register(Request $request)
    {
        // Validar los datos de entrada

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);


        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 422);
        }

        // Crear el nuevo usuario
        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Guardar el usuario en la base de datos
        $user->save();

        // Retornar una respuesta de éxito
        return response()->json(['message' => 'Usuario registrado exitosamente'], 201);
    }
}
