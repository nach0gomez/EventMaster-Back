<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

class PasswordController extends Controller
{
    public function recovery(Request $request)
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
        
        if ($validator->passes()) {

            //DB::beginTransaction();

            try {

                // Generar contraseña aleatoria
                $newPassword = $this->generateRandomPassword();
                // Hashear la nueva contraseña
                $hashedPassword = Hash::make($newPassword);
                // Actualizar la contraseña del usuario
                User::where("email", $request->email)->update(['password' => $hashedPassword]);
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
                $mail->Subject = 'Recuperacion De Contrasena';
                $mail->Body    =    'Hola, hemos recibido una solicitud para restablecer su contraseña.<br>'
                                    . 'Su nueva contraseña es: <b>' . $newPassword . '</b><br>'
                                    . 'Por favor cambie su contraseña en su próximo inicio de sesión.'. '</b><br>'
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
    /*creacion aleatoria de contraseña*/
    public function generateRandomPassword($length = 8)
    {
        $characters = '0123456789abcd';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

}

