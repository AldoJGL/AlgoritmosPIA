<?php
// Iniciar sesión (necesario para usar $_SESSION)
session_start();

// Incluir conexión a la base de datos
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Consultar el usuario en la base de datos
    $sql = "SELECT id_usuario, nombre, password FROM Usuarios WHERE email = '$email'";
    $result = $conn->query($sql);

    // Comprobar si la consulta fue exitosa
    if ($result === false) {
        // Si la consulta falla, mostrar el error
        die("Error en la consulta: " . $conn->error);
    }

    // Verificar si el usuario existe
    if ($result->num_rows > 0) {
        // El usuario existe, comprobar la contraseña
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            // Contraseña correcta, iniciar sesión
            $_SESSION['id_usuario'] = $row['id_usuario'];
            $_SESSION['nombre'] = $row['nombre'];
            header("Location: inicio.php"); // Redirigir al usuario a su panel
            exit();
        } else {
            // Contraseña incorrecta
            echo "Contraseña incorrecta.";
        }
    } else {
        // Usuario no encontrado
        echo "No se encontró una cuenta con ese correo electrónico.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
</head>
<body>
    <h1>Iniciar Sesión</h1>
    <form method="POST" action="login.php">
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required><br>

        <label for="password">Contraseña:</label>
        <input type="password" name="password" id="password" required><br>

        <input type="submit" value="Iniciar Sesión">
    </form>
</body>
</html>
