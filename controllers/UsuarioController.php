<?php

namespace Controllers;

use Model\Usuario;
use MVC\Router;

class UsuarioController{
    public static function index(Router $router){

        isAuth();

        $id = $_SESSION['id'];
        $usuario = Usuario::find($id);
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();

            if(empty($alertas)){
                $usuario->hashPassword();
                $usuario->guardar();
                $_SESSION['nombre'] = $usuario->nombre . " " . $usuario->apellido;
                $_SESSION['email'] = $usuario->email;
                if($usuario->admin === '0'){
                    header('location: /cita');
                }else if($usuario->admin === '1'){
                    header('location: /admin');
                }
                
            }
        }

        $router->render('usuario/index', [
            'alertas' => $alertas,
            'usuario' => $usuario
        ]);
    }
}