<?php

namespace Controllers;

use MVC\Router;

class CitaController{
    public static function index(Router $router){
        $nombre = $_SESSION['nombre'];
        $id = $_SESSION['id'];

        $datos = [
            'nombre' => $nombre,
            'id' => $id
        ];

        isAuth();
        $router->render('cita/index', $datos);
    }
}