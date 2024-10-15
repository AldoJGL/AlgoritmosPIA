<?php
session_start();

// Verificar si la sesión está iniciada
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

include 'db.php'; // Conexión a la base de datos

// Inicializar variables para mensajes de error o éxito
$error = '';
$success = '';

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_cuenta = $_POST['id_cuenta'];
    $descripcion = $_POST['descripcion'];
    $monto_total = $_POST['monto_total'];
    $monto_mensual = $_POST['monto_mensual'];
    $meses_restantes = $_POST['meses_restantes'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];

    // Validar los campos
    if (empty($descripcion) || empty($monto_total) || empty($monto_mensual) || empty($meses_restantes) || empty($fecha_inicio) || empty($fecha_fin)) {
        $error = "Por favor, complete todos los campos.";
    } else {
        // Insertar la compra en la base de datos
        $sql = "INSERT INTO Compras_a_Meses (id_cuenta, descripcion, monto_total, monto_mensual, meses_restantes, fecha_inicio, fecha_fin) 
                VALUES ('$id_cuenta', '$descripcion', '$monto_total', '$monto_mensual', '$meses_restantes', '$fecha_inicio', '$fecha_fin')";
        
        if ($conn->query($sql) === TRUE) {
            $success = "Compra agregada exitosamente.";
            // Redirigir a inicio.php después de agregar la compra
            header("Location: inicio.php");
            exit();
        } else {
            $error = "Error al agregar la compra: " . $conn->error;
        }
    }
}

// Obtener cuentas del usuario para el dropdown
$id_usuario = $_SESSION['id_usuario'];
$sql_cuentas = "SELECT * FROM Cuentas_Bancarias WHERE id_usuario = '$id_usuario'";
$result_cuentas = $conn->query($sql_cuentas);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Compra a Meses</title>
</head>
<body>

<header>
    <h1>Agregar Compra a Meses</h1>
</header>

<section>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <?php if ($error): ?>
            <p style="color:red;"><?php echo $error; ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
            <p style="color:green;"><?php echo $success; ?></p>
        <?php endif; ?>

        <label for="id_cuenta">Selecciona la Cuenta:</label>
        <select name="id_cuenta" id="id_cuenta" required>
            <option value="">Selecciona una cuenta</option>
            <?php while ($row_cuenta = $result_cuentas->fetch_assoc()): ?>
                <option value="<?php echo $row_cuenta['id_cuenta']; ?>">
                    <?php echo $row_cuenta['numero_tarjeta'] . ' - ' . $row_cuenta['banco']; ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="descripcion">Descripción:</label>
        <input type="text" name="descripcion" id="descripcion" required>

        <label for="monto_total">Monto Total:</label>
        <input type="number" name="monto_total" id="monto_total" step="0.01" required>

        <label for="monto_mensual">Monto Mensual:</label>
        <input type="number" name="monto_mensual" id="monto_mensual" step="0.01" required>

        <label for="meses_restantes">Meses Restantes:</label>
        <input type="number" name="meses_restantes" id="meses_restantes" required>

        <label for="fecha_inicio">Fecha de Inicio:</label>
        <input type="date" name="fecha_inicio" id="fecha_inicio" required>

        <label for="fecha_fin">Fecha de Fin:</label>
        <input type="date" name="fecha_fin" id="fecha_fin" required>

        <button type="submit">Agregar Compra</button>
    </form>
</section>
<a href="inicio.php">Regresar</a>
</body>
</html>

<?php
// Cerrar la conexión a la base de datos
$conn->close();
?>
