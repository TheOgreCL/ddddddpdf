<?php

require_once "conexion.php";

// Verificar que la solicitud sea POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit("Método no permitido.");
}
//validacion del id
$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    exit("ID no válido.");
}

$sql = "DELETE FROM usuarios WHERE id = :id";
$stmt = $conexion->prepare($sql);
$stmt->execute(["id" => $id]);

// Redirige al index de chill
if ($stmt->rowCount() > 0) {
    header("Location: index.php?mensaje=eliminado");
} else {
    header("Location: index.php");
}

exit;