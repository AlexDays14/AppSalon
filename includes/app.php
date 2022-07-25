<?php 

require 'funciones.php';
require 'database.php';
require __DIR__ . '/../vendor/autoload.php';

date_default_timezone_set('America/Ojinaga');

// Conectarnos a la base de datos
use Model\ActiveRecord;
ActiveRecord::setDB($db);