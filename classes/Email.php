<?php

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;


class Email{

    public $email;
    public $nombre;
    public $token;

    public function __construct($email, $nombre, $token)
    {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;
    }

    public function enviarConfirmacion(){
        // Crear el objeto de mail

        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = '482a1107094bbd';
        $mail->Password = '8c06920efb0f6c';

        $mail->setFrom('proferna8@hotmail.com');
        $mail->addAddress('proferna8@hotmail.com', 'AppSalon.com');
        $mail->Subject = 'Confirma tu cuenta';

        // Set HTML
        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';

        $contenido = "<html>";
        $contenido .= "<p><strong>Hola " . $this->nombre . "</strong> Has creado tu cuenta en AppSalon, solo debes confirmarla presionando el siguiente enlace</p>";
        $contenido .= "<p>Presiona Aquí: <a href='http://127.0.0.1:3000/confirmar-cuenta?token=" . $this->token . "'>Confirmar Cuenta</a> </p>";
        $contenido .= "<p>Si tu no solicitaste esta cuenta, puedes ignorar el mensaje.</p>";
        $contenido .= "</html>";
        $mail->Body = $contenido;

        // Enviar el email
        $mail->send();
    }

    public function enviarInstrucciones(){

        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = '2e191b78c9a5e4';
        $mail->Password = '0903462088730a';

        $mail->setFrom('proferna8@hotmail.com');
        $mail->addAddress('alexdays142612@gmail.com', 'AppSalon.com');
        $mail->Subject = 'Reestablece tu password';

        // Set HTML
        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';

        $contenido = "<html>";
        $contenido .= "<p><strong>Hola " . $this->nombre . "</strong> Has solicitado reestablecer tu password, da click en el siguiente enlace para hacerlo.</p>";
        $contenido .= "<p>Presiona Aquí: <a href='http://localhost:3000/recuperar?token=" . $this->token . "'>Reestablecer Password</a> </p>";
        $contenido .= "<p>Si tu no solicitaste esta acción, puedes ignorar el mensaje.</p>";
        $contenido .= "</html>";
        $mail->Body = $contenido;

        // Enviar el email
        $mail->send();
    }

}