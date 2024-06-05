<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use JWTAuth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Person;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';


/**
 * @group Users
 * 
 * APIs for managing Users_Controller 
 */
class UserController extends Controller
{
    public function __construct()
    {
       //este middleware permite que solo los usuarios autenticados puedan acceder a los metodos del controlador
       //y las excepciones que se encuentran en el except
       $this->middleware('auth:sanctum')->except('addNewUser','generateRandomPassword'.'updatePassword','emailValidatorCode','generateRandomValidator');
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
            'password' => 'required|confirmed|string|max:20',
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
                $person->email_verificate_confirm=$request->email_verificate_confirm;
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

    public function editPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|max:50',
            'password_old' => 'required|string|max:30|min:6',
            'password' => 'required|confirmed|string|max:20|min:6',
        ], [
            'password_old.required' => 'La contraseña antigua es requerida.',
            'password_old.min' => 'La contraseña antigua debe tener al menos 6 caracteres.',
            'password_old.max' => 'La contraseña antigua debe tener como máximo 30 caracteres.',
            'password.min' => 'La contraseña nueva debe tener al menos 6 caracteres.',
            'password.required' => 'La contraseña es requerida.',
            'password.confirmed' => 'Las contraseñas no coiciden.',
        ]);

        if ($validator->passes()) {

            $user = User::where('email', $request->email)->first();
            //DB::beginTransaction();
            if (!$user || !Hash::check($request->password_old, $user->password)) {
                return response()->json(['message' => 'Contraseña Incorrecta'], Response::HTTP_UNAUTHORIZED);
            }
            try {
                $person = User::where('email', $request->email)->first();
                $person->password = Hash::make($request->password);
                $person->save();

                return response()->json([
                    'msg' => 'Contraseña Actualizada con exito'
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

    public function editUser(Request $request)
{
            $validator = Validator::make($request->all(), [
                'id_user' => 'required|numeric',
                'first_name' => 'required|string|max:30',
                'middle_name' => 'nullable|string|max:30',
                'last_name' => 'required|string|max:30',
                'second_last_name' => 'nullable|string|max:30',
                'document' => [
                    'required',
                    'numeric',
                    Rule::unique('users', 'document')->ignore($request->id_user, 'id_user'),
                ],
                'email' => [
                    'required',
                    'string',
                    Rule::unique('users', 'email')->ignore($request->id_user, 'id_user'),
                ],
                'username' => [
                    'required',
                    'string',
                    Rule::unique('users', 'username')->ignore($request->id_user, 'id_user'),
                ],
                'is_eplanner' => 'required|boolean',
                'is_eattendee' => 'required|boolean',
            ], [
                'username.unique' => 'El nombre de usuario ya esta en uso.',
            'document.unique' => 'El documento ya esta registrado en nuestra base de datos.',
            'email.unique' => 'El correo electronico ya esta registrado en nuestra base de datos.',
            ]);

    if ($validator->fails()) {
        return response()->json($validator->errors()->all(), 422);
    }

    try {
        // Recuperar el usuario actual por id_user
        $person = User::findOrFail($request->id_user);
         // Actualizar los datos del usuario
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

        return response()->json(['msg' => 'Persona actualizada con éxito'], 200);

    } catch (Exception $e) {
        return response()->json(['res' => false, 'msg' => $e->getMessage()], 422);
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

    public function generateRandomValidator($length = 8)
    {
        $characters = '0123456789';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    public function codeValidator(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id_user' => 'required|numeric|exists:users,id_user',
            'code_verificate' => 'required|string|max:8|min:8',
        ], [
            'code_verificate.required' => 'El email es requerido',
            'code_verificate.string' => 'El email debe ser un texto',
            'code_verificate.max' => 'El email debe tener maximo 8 caracteres',
            'code_verificate.min' => 'El email debe tener minimo 8 caracteres',
            'id_user.exists' => 'El usuario no esta registrado',
        ]);
        
        if ($validator->passes()) {

            //DB::beginTransaction();

            try 
         {
                $person = User::findOrFail($request->id_user);
                if ($person->email_verificate == $request->code_verificate) {
                    $person->email_verificate_confirm = true;
                    $person->save();
                    return response()->json([
                        'res' => true,
                        'msg' => 'Correo verificado con exito'
                    ]);
                } else {
                    return response()->json([
                        'res' => false,
                        'msg' => 'El codigo no coincide'
                    ], 422);
                }
         }
            catch (Exception $e) {
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
    
    public function emailValidatorCode(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:50|exists:users,email',
            
        ], [
            'email.required' => 'El email es requerido',
            'email.string' => 'El email debe ser un texto',
            'email.email' => 'El email debe ser un email valido',
            'email.max' => 'El email debe tener maximo 50 caracteres',
            'email.exists' => 'El email no esta registrado',
            
        ]);

        if ($validator->passes()) 
        {
            try {
                 // Generar el codigo aleatorio
                 $validatorConfirm = $this->generateRandomValidator();

                    $person = User::where('email', $request->email)->firstOrFail();
                    // guardamos el codigo en el campo email_verificate de la persona
                    $person->email_verificate = $validatorConfirm;
                    $person->save();
               //creamos el email
               $mail = new PHPMailer(true);

                //configuraciones del servidor
                $mail->SMTPDebug = 0;  //0 para no ver o SMTP::DEBUG_SERVER para debuguear       
                $mail->isSMTP();                                            //enviar usando SMTP
                $mail->Host       = 'mail.demo2.linkisite.com';                    // el servidor de correo que vamos a usar 
                $mail->SMTPAuth   = true;                                   // permitir SMTP autentificacion
                $mail->Username   = 'notificaciones@demo2.linkisite.com';                     //SMTP usuario
                $mail->Password   = 'tR%0lQ?l7Z&t';                               //SMTP password
                $mail->SMTPSecure = 'ssl';                                    //permitir encriptacion implicita TLS
                $mail->Port       = 465;                                    //TCP puerto de conexion

                //destinatarios
                $mail->setFrom('notificaciones@demo2.linkisite.com', 'Event Master');// remitente
                $mail->addAddress($request->email);     //destinatario
                

                //Contenido
                $mail->isHTML(true);                                  //poner el email formato para HTML
                $mail->Subject = 'Codigo De Validacion De Correo Electronico';
                $mail->Body    =    'Hola! Gracias por registrarte en EVENT MASTER.<br>'
                                    . 'Por favor, confirma tu correo electronico para poder acceder a tu cuenta.<br>'              
                                    . 'Tu codigo de confirmacion es: <b>' . $validatorConfirm . '</b><br>'
                                    . ' <b><a href="https://demo2.linkisite.com/login">Ir al Sitio</a>'
                                    . '<br><br>Saludos,<br>Event Master'
                                   ;
                

                //enviar el correo
                $mail->send();
                

                return response()->json([
                    'res' => true,
                    'msg' => 'Correo enviado con exito'
                ]);
                //    DB::commit();
                
            } catch (Exception $e) {
                //DB::rollback();
                return response()->json([
                    'res' => false,
                    'msg' => $mail->ErrorInfo
                ], 422);
            }
        }
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 422);
        }
    }

}
