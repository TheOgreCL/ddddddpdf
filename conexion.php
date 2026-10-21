<?php
//ya si igual quedo bonito un poco mas corto
$host = "localhost";
$user = "root";
$password = "";
$db = "crudphp";

try{
    $conexion = new PDO("mysql:host=$host;dbname=$db", $user, $password);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log($e->getMessage());
    exit("Error al conectar con la base de datos.");
}
