<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{

    public function test(Request $request)
    {
        return response()->json(['message' => 'Hello World!']);
    }

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
}
