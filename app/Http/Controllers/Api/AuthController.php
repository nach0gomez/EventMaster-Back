<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\Response; 



class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        //este middleware permite que solo los usuarios autenticados puedan acceder a los metodos del controlador
        $this->middleware('auth:sanctum')->except('login','refresh','register');
    }
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
       if(!Auth::attempt($request->only('email', 'password'))){
            return response()->json(['message' => 'No autorizado'], Response::HTTP_UNAUTHORIZED);
       }
         $user = Auth::user();
         $existsUser = User::where("email",$request->email)->where("status",true)->get()->first();

            $token = $user->createToken('Token')->plainTextToken;
            $cookie = cookie('jwt', $token, 60*2); // 2 horas
            return response()->json(['token' => $token,
                                        'user'=>$existsUser])->withCookie($cookie);
    }
    

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
{
    if (!$user = Auth::user()) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    return response()->json($user);
}

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $user = Auth::user();
        
        // Revocar todos los tokens del usuario
        $user->tokens()->delete();
        
        // Eliminar la cookie jwt
        $cookie = \Cookie::forget('jwt');
        
        return response()->json(['message' => 'Logged out successfully'])->withCookie($cookie);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()//nuevo token
    {
        
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */

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