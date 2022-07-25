<?php

function debuguear($variable) : string {
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
    exit;
}

// Escapa / Sanitizar el HTML
function s($html) : string {
    $s = htmlspecialchars($html);
    return $s;
}

function esUltimo(string $actual, string $proximo) : bool {
    if($proximo !== $actual){
        return true;
    }
    return false;
}

// funcion que revisa que el usuario esta autenticado
function isAuth() : void {
    if(!isset($_SESSION['login'])){
        header('location: /');
    }
}

function isLogin() : void{
    if(isset($_SESSION)){
    $login = $_SESSION['login'] ?? null;
    $admin = $_SESSION['admin'] ?? null;
        if($login === true && $admin === null){
            header('location: /cita');
        }else if($login === true && $admin === '1'){
            header('location: /admin');
        }
    }else{
    header('location: /');
    }
}

function isAdmin() : void {
    if(!isset($_SESSION['admin'])){
        header('location: /cita');
    }
}