<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'app/Mail/PHPMailer/Exception.php';
require 'app/Mail/PHPMailer/PHPMailer.php';
require 'app/Mail/PHPMailer/SMTP.php';

class PasswordController extends Controller
{
    public function recovery(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'Subject'=>'required|string|max:50|min:5',
            'Body'=>'required|string|max:255|min:10',
            'email' => 'required|string|email|max:50|exists:users,email ',
        ], [
            'Subject.required' => 'El asunto es requerido',
            'Subject.string' => 'El asunto debe ser un texto',
            'Subject.max' => 'El asunto debe tener maximo 50 caracteres',
            'Subject.min' => 'El asunto debe tener minimo 5 caracteres',
            'Body.required' => 'El cuerpo del mensaje es requerido',
            'Body.string' => 'El cuerpo del mensaje debe ser un texto',
            'Body.max' => 'El cuerpo del mensaje debe tener maximo 255 caracteres',
            'Body.min' => 'El cuerpo del mensaje debe tener minimo 10 caracteres',
           'email.required' => 'El email es requerido',
            'email.string' => 'El email debe ser un texto',
            'email.email' => 'El email debe ser un email valido',
            'email.max' => 'El email debe tener maximo 50 caracteres',
            'email.exists' => 'El email no esta registrado',
        ]);

        if ($validator->passes()) {

            //DB::beginTransaction();
            
            try {
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
                $mail->Subject = $request->Subject;
                $mail->Body    = $request->Body;

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
