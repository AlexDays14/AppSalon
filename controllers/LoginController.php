<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController {
    public static function login(Router $router){

        isLogin();

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $auth = new Usuario($_POST);

            $alertas = $auth->validarLogin();

            if(empty($alertas)){
                //comprobar que exista el usuario
                $usuario = Usuario::where('email', $auth->email);

                if($usuario){
                    // Verificar el password
                    if($usuario->comprobarPasswordAndVerificado($auth->password)){
                        // Autenticar el usuario
                        

                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre . " " . $usuario->apellido;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        // Redireccionamiento
                        if($usuario->admin === "1"){
                            $_SESSION['admin'] = $usuario->admin ?? null;
                            header('location: /admin');
                        }else{
                            header('location: /cita');
                        }
                    }
                }else{
                    Usuario::setAlerta('error', 'Usuario no encontrado');
                }
            }
        }

        $alertas = Usuario::getAlertas();

        $datos = [
            'alertas' => $alertas
        ];
        $router->render('auth/login', $datos);
    }

    public static function logout(){
        $_SESSION = [];

        header('location: /');

    }

    public static function olvide(Router $router){

        isLogin();

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $auth = new Usuario($_POST);
            $alertas = $auth->validarEmail();

            if(empty($alertas)){
                $usuario = Usuario::where('email', $auth->email);
                
                if($usuario && $usuario->confirmado === "1"){
                    $usuario->crearToken();
                    $usuario->guardar();

                    // Enviar el email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();

                    // Alerta
                    Usuario::setAlerta('exito', 'Te Enviamos un Email con para Reestablecer tu Password');
                }else{
                    Usuario::setAlerta('error', 'El Usuario no Existe o no está Confirmado');
                }
            }
        }

        $alertas = Usuario::getAlertas();

        $datos = [
            'alertas' => $alertas
        ];
        $router->render('auth/olvide-password', $datos);
    }

    public static function recuperar(Router $router){

        isLogin();
        if(!isset($_GET['token'])){
            header('location: /olvide');
        }

        $alertas = [];
        $error = false;

        $token = s($_GET['token']);

        // Buscar usuario por su token
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)){
            Usuario::setAlerta('error', 'Token No Válido');
            $error = true;
        }
        
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            // Leer el nuevo password y guardarlo
            $password = new Usuario($_POST);
            $alertas = $password->validarPassword();

            if(empty($alertas)){

                $usuario->password = null;
                $usuario->password = $password->password;
                $usuario->hashPassword();
                $usuario->token = null;

                $resultado = $usuario->guardar();
                if($resultado){
                    header('location: /');
                }

            }

        }

        $alertas = Usuario::getAlertas();
        $datos = [
            'alertas' => $alertas,
            'error' => $error
        ];
        $router->render('auth/recuperar-password', $datos);
    }

    public static function crear(Router $router){

        isLogin();

        $usuario = new Usuario;

        //Alertas vacías
        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();

            // Revisar que alerta esté vacío
            if(empty($alertas)){
                // Verificar si el usuario no existe
                $resultado = $usuario->existeUsuario();

                if($resultado->num_rows){
                    $alertas = Usuario::getAlertas();
                }else{
                    // Hashear el password
                    $usuario->hashPassword();

                    // Generar un token único
                    $usuario->crearToken(); 

                    // Enviar email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);

                    $email->enviarConfirmacion();

                    // Crear usuario
                    $resultado = $usuario->guardar();
                    if($resultado){
                        header('location: /mensaje');
                    }
                }
            }
        }

        $datos = [
            'usuario' => $usuario,
            'alertas' => $alertas
        ];
        $router->render('auth/crear-cuenta', $datos);
    }

    public static function mensaje(Router $router){
        $router->render('auth/mensaje');
    }

    public static function confirmar(Router $router){

        $alertas = [];

        $token = s($_GET['token']);
        $usuario = Usuario::where('token', $token);
        
        if(empty($usuario)){
            //mostrar mensaje error
            Usuario::setAlerta('error', 'Token no válido');
        }else{
            //modificar a usuario confirmado
            $usuario->confirmado = '1';
            $usuario->token = null;
            $usuario->guardar();
            Usuario::setAlerta('exito', 'Cuenta Confirmada Correctamente');
            header('location : /');
        }

        $alertas = Usuario::getAlertas();
        $datos = [
            'alertas' => $alertas
        ];
        $router->render('auth/confirmar-cuenta', $datos);
    }
}