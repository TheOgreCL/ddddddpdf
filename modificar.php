<?php

require_once "conexion.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    exit("ID no válido.");
}

// Preparar y ejecutar la consulta para obtener el usuario
$stmt = $conexion->prepare(
    "SELECT id, correo FROM usuarios WHERE id = :id"
);
$stmt->execute(["id" => $id]);
$usuario = $stmt->fetch();

if (!$usuario) {
    http_response_code(404);
    exit("Usuario no encontrado.");
}

//Mensajes de error, etc
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $correo = isset($_POST["correo"]) ? trim($_POST["correo"]) : "";
    $contrasena = isset($_POST["contrasena"]) ? $_POST["contrasena"] : "";

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "Ingresa un correo válido.";
    } else {
        try {
            if ($contrasena !== "") {
                $hash = password_hash($contrasena, PASSWORD_DEFAULT);

                $sql = "UPDATE usuarios
                        SET correo = :correo, contrasena = :contrasena
                        WHERE id = :id";

                $stmt = $conexion->prepare($sql);
                $stmt->execute([
                    "correo" => $correo,
                    "contrasena" => $hash,
                    "id" => $id
                ]);
            } else {
                $sql = "UPDATE usuarios
                        SET correo = :correo
                        WHERE id = :id";

                $stmt = $conexion->prepare($sql);
                $stmt->execute([
                    "correo" => $correo,
                    "id" => $id
                ]);
            }

            header("Location: index.php?mensaje=actualizado");
            exit;

        } catch (PDOException $e) {
            if ($e->getCode() === "23000") {
                $mensaje = "Ese correo ya está registrado.";
            } else {
                error_log($e->getMessage());
                $mensaje = "No se pudo actualizar el usuario.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar usuario</title>
</head>
<body>

    <h1>Editar usuario</h1>

    <?php if ($mensaje !== ""): ?>
        <p><?= htmlspecialchars($mensaje, ENT_QUOTES, "UTF-8") ?></p>
    <?php endif; ?>

    <form method="POST" action="modificar.php?id=<?= (int)$id ?>">

        <label for="correo">Correo electrónico:</label>
        <input type="email" id="correo" name="correo"
               value="<?= htmlspecialchars($usuario["correo"], ENT_QUOTES, "UTF-8") ?>"
               required>

        <label for="contrasena">Nueva contraseña (opcional):</label>
        <input type="password" id="contrasena" name="contrasena" autocomplete="new-password">

        <button type="submit">Guardar cambios</button>
    </form>

    <p><a href="index.php">Volver al listado</a></p>

</body>
</html>