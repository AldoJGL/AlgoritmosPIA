<?php
$servername = "localhost"; // Cambia si es necesario
$username = "root"; // Tu usuario
$password = ""; // Tu contraseña
$dbname = "basedatos"; // Nombre de tu base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>