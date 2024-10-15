<?php
session_start();
include 'db.php'; // Conexión a la base de datos

// Verificar si la sesión está iniciada
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_usuario = $_SESSION['id_usuario']; // Obtener ID de usuario de la sesión
    $tipo = $_POST['tipo'];
    $numero_tarjeta = $_POST['numero_tarjeta'];
    $banco = $_POST['banco'];
    $saldo_disponible = $_POST['saldo_disponible'];
    $limite_credito = $_POST['limite_credito'];
    $fecha_corte = $_POST['fecha_corte'];
    $fecha_pago = $_POST['fecha_pago'];
    $saldo_actual = $_POST['saldo_actual']; // Saldo actual inicial

    // Consulta para insertar la nueva cuenta bancaria
    $sql = "INSERT INTO Cuentas_Bancarias (id_usuario, tipo, numero_tarjeta, banco, saldo_disponible, limite_credito, fecha_corte, fecha_pago, saldo_actual) 
            VALUES ('$id_usuario', '$tipo', '$numero_tarjeta', '$banco', '$saldo_disponible', '$limite_credito', '$fecha_corte', '$fecha_pago', '$saldo_actual')";

    if ($conn->query($sql) === TRUE) {
        header("Location: inicio.php");
        exit(); // Asegúrate de que no se ejecute más código
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}

?>

<!-- Formulario HTML -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Cuenta</title>
</head>
<body>

<header>
    <h1>Agregar Cuenta Bancaria</h1>
</header>

<section class="agregar-cuenta">
    <form method="POST" action="addcuenta.php">
        <input type="hidden" name="id_usuario" value="<?php echo $_SESSION['id_usuario']; ?>"><!-- ID Usuario oculto -->
        Tipo: <select name="tipo" required>
            <option value="Crédito">Crédito</option>
            <option value="Débito">Débito</option>
        </select><br>
        Número de Tarjeta: <input type="text" name="numero_tarjeta" required><br>
        Banco: <input type="text" name="banco" required><br>
        Saldo Disponible(No se llena si es tarjeta de credito): <input type="text" name="saldo_disponible" required><br>
        Límite de Crédito: <input type="text" name="limite_credito" required><br>
        Fecha de Corte: <input type="date" name="fecha_corte" required><br>
        Fecha de Pago: <input type="date" name="fecha_pago" required><br>
        Saldo Actual(Si es de debito es igual al saldo disponible): <input type="text" name="saldo_actual" value="0" required><br> <!-- Saldo actual inicial -->
        <input type="submit" value="Agregar Cuenta">
    </form>
</section>
<a href="inicio.php">Regresar</a>
</body>
</html>
