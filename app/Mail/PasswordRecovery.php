
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'app/Mail/PHPMailer/Exception.php';
require 'app/Mail/PHPMailer/PHPMailer.php';
require 'app/Mail/PHPMailer/SMTP.php';


//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = 0;  //0 para no ver o SMTP::DEBUG_SERVER para debuguear       
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'mail.demo2.linkisite.com';                     //Set the SMTP server to send through// el servidor de correo que vamos a usar 
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'notificaciones@demo2.linkisite.com';                     //SMTP username
    $mail->Password   = 'tR%0lQ?l7Z&t';                               //SMTP password
    $mail->SMTPSecure = 'ssl';            //Enable implicit TLS encryption
    $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('notificaciones@demo2.linkisite.com', 'Event Master');
    $mail->addAddress('dinomax3@outlook.com');     //Add a recipient

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = 'Recuperacion de contrasena Event Master';
    $mail->Body    = '<b>recuperacion de contrasena Event Master in bold!</b>';

    $mail->send();
    echo 'Correo enviado correctamente';
} catch (Exception $e) {
    echo "Ocurrio un error al envia el correo: {$mail->ErrorInfo}";
}