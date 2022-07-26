<?php

$db = mysqli_connect('us-cdbr-east-06.cleardb.net', 'bb4e01a00478fa', 'ec614b5a', 'heroku_43974f7748d8596');


if (!$db) {
    echo "Error: No se pudo conectar a MySQL.";
    echo "errno de depuración: " . mysqli_connect_errno();
    echo "error de depuración: " . mysqli_connect_error();
    exit;
}
