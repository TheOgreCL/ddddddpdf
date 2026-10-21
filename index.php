<?php
//creacion de usuario con restricciones basicas al momento de crearlo
require_once "conexion.php";
$mensaje = "";

require_once 'dompdf/autoload.inc.php';




//creacion del usuario en el mismo archivo, ya fue, solo pide correo y contraseña; id y fecha son añadidos de manera automatica
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $correo = isset($_POST["correo"]) ? trim($_POST["correo"]) : "";
    $contrasena = isset($_POST["contrasena"]) ? $_POST["contrasena"] : "";
//validacion de correo y contraseña
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "Ingresa un correo válido.";
    } else {
        try {
            $hash = password_hash($contrasena, PASSWORD_DEFAULT);

            $sql = "INSERT INTO usuarios (correo, contrasena) VALUES (:correo, :contrasena)";

            $stmt = $conexion->prepare($sql);
            $stmt->execute(["correo" => $correo, "contrasena" => $hash
            ]);

            header("Location: index.php?mensaje=creado");
            exit;

//manejo de errores
        } catch (PDOException $e) {
            if ($e->getCode() === "23000") {
                $mensaje = "Ese correo ya está registrado.";
            } else {
                error_log($e->getMessage());
                $mensaje = "No se pudo registrar el usuario.";
            }
        } catch (PDOException $e) {
            if ($e->getCode() === "23000") {
                $mensaje = "Ese correo ya está registrado.";
            } else {
                error_log($e->getMessage());
                $mensaje = "No se pudo registrar el usuario.";
            }
        }
    }
}

$sql = "SELECT id, correo, fecha_registro
        FROM usuarios
        ORDER BY id DESC";

$stmt = $conexion->query($sql);
$usuarios = $stmt->fetchAll();

echo 'PHP version: ' . phpversion();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD</title>
</head>
<body>
    CRUD
    <div>
    <form method="POST" action="index.php">
        <label for="correo">Correo electrónico:</label><br>
        <input type="email" id="correo" name="correo" required>
        <br>
        <label for="contrasena">Contraseña:</label><br>
        <input type="password" id="contrasena"
               name="contrasena" required>
        <br>
        <button type="submit">Crear usuario</button>
    </form>
    <br>
    <br>
    Lista de registros
    <div>
        <table border="1" cellpadding="8" cellspacing="0">
            <tr>
                <th>id</th>
                <th>usuario</th>
                <th>fecha de creacion</th>
                <th>Acciones</th>
            </tr>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <!-- Aprendi hoy sobre esta cosa, hace que convierta caracter especiales en html para mas seguridad -->
                        <td><?= htmlspecialchars((string)$usuario["id"]) ?></td>
                        <td><?= htmlspecialchars($usuario["correo"], ENT_QUOTES, "UTF-8") ?></td>
                        <td><?= htmlspecialchars($usuario["fecha_registro"], ENT_QUOTES, "UTF-8") ?></td>
                        <td><a href="modificar.php?id=<?= (int)$usuario["id"] ?>"> Editar</a>
                            <form method="POST" action="eliminar.php"
                                style="display:inline"
                                onsubmit="return confirm('¿Eliminar este usuario?');">
                                <input type="hidden" name="id"
                                    value="<?= (int)$usuario["id"] ?>">
                                <button type="submit">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($usuarios)): ?>
                    <tr><td colspan="4">No hay usuarios registrados.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        <a href="generar_pdf.php" target="_blank">Generar PDF</a>
        <br>test images<br>
        <img src='20240109_040938.jpg' width="auto" height="auto"/> <img src='iavoy.jpg' width="auto" height="auto"/>
    </div>    
    
    </div>

</body>
</html>
