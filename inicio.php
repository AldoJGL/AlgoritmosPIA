<?php
session_start();

// Verificar si la sesión está iniciada
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

include 'db.php'; // Conexión a la base de datos

$id_usuario = $_SESSION['id_usuario']; // ID del usuario de la sesión

// Consulta para obtener el nombre del usuario
$sql_usuario = "SELECT nombre FROM Usuarios WHERE id_usuario = '$id_usuario'";
$result_usuario = $conn->query($sql_usuario);
$nombre_usuario = '';
if ($result_usuario && $result_usuario->num_rows > 0) {
    $row_usuario = $result_usuario->fetch_assoc();
    $nombre_usuario = $row_usuario['nombre']; // Nombre del usuario
}

// Consulta para obtener cuentas del usuario
$sql_cuentas = "SELECT * FROM Cuentas_Bancarias WHERE id_usuario = '$id_usuario'";
$result_cuentas = $conn->query($sql_cuentas);

// Consulta para obtener compras a meses
$sql_compras = "SELECT * FROM Compras_a_Meses WHERE id_cuenta IN (SELECT id_cuenta FROM Cuentas_Bancarias WHERE id_usuario = '$id_usuario')";
$result_compras = $conn->query($sql_compras);

// Consulta para obtener recordatorios de pago
$sql_recordatorios = "SELECT * FROM Recordatorios_Pagos WHERE id_usuario = '$id_usuario'";
$result_recordatorios = $conn->query($sql_recordatorios);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organiza tu Dinero</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<header>
<nav>
    <ul style="list-style-type: none; padding: 0; margin: 0;">
        <li style="display: inline; margin-right: 20px;">
            <a href="inicio.php" style="text-decoration: none; color: #333;">Inicio</a>
        </li>
        <li style="display: inline; margin-right: 20px;">
            <a href="addcuenta.php" style="text-decoration: none; color: #333;">Agregar Cuenta</a>
        </li>
        <li style="display: inline; margin-right: 20px;">
            <a href="addcompra.php" style="text-decoration: none; color: #333;">Agregar Compra a Meses</a>
        </li>
        <li style="display: inline; margin-right: 20px;">
            <a href="logout.php" style="text-decoration: none; color: #f00;">Cerrar Sesión</a>
        </li>
    </ul>
</nav>
</header>

<h1>Hola, <?php echo htmlspecialchars($nombre_usuario); ?>!</h1>

<section class="resumen-financiero">

<?php
// Verificamos si hay cuentas
if ($result_cuentas && $result_cuentas->num_rows > 0) {
    // Inicializar variables para el resumen financiero
    $saldo_total = 0;
    $deudas_total = 0;

    // Mostrar detalles de cada cuenta
    while ($row_cuenta = $result_cuentas->fetch_assoc()) {
        $saldo_total += $row_cuenta['saldo_disponible'];
        if ($row_cuenta['tipo'] == 'Crédito') {
            $deudas_total += $row_cuenta['saldo_actual']; // Asumimos que aquí se guardan las deudas
        }
        ?>
        <h2>Detalles de la Cuenta</h2>
        <p><strong>Tipo:</strong> <?php echo $row_cuenta['tipo']; ?></p>
        <p><strong>Número de Tarjeta:</strong> <?php echo $row_cuenta['numero_tarjeta']; ?></p>
        <p><strong>Banco:</strong> <?php echo $row_cuenta['banco']; ?></p>
        <p><strong>Saldo Disponible:</strong> $<?php echo number_format($row_cuenta['saldo_disponible'], 2); ?></p>
        <p><strong>Límite de Crédito:</strong> $<?php echo number_format($row_cuenta['limite_credito'], 2); ?></p>
        <p><strong>Saldo Actual:</strong> $<?php echo number_format($row_cuenta['saldo_actual'], 2); ?></p>
        <hr>
        <?php
    }

    // Mostrar el resumen financiero
    ?>
    <h2>Resumen Financiero</h2>
    <p><strong>Saldo Total Disponible:</strong> $<?php echo number_format($saldo_total, 2); ?></p>
    <p><strong>Deudas Pendientes:</strong> $<?php echo number_format($deudas_total, 2); ?></p>
    
    <h2>Resumen de Tarjetas de Crédito</h2>
    <table>
        <tr>
            <th>Número de Tarjeta</th>
            <th>Banco</th>
            <th>Límite de Crédito</th>
            <th>Saldo Disponible</th>
            <th>Fecha de Corte</th>
            <th>Fecha de Pago</th>
            <th>Estado de Pagos</th>
        </tr>

    <?php
    // Obtener la fecha actual
    $fecha_actual = date('Y-m-d');

    // Mostrar detalles de cada tarjeta de crédito
    $result_cuentas->data_seek(0); // Reiniciar el puntero del resultado para volver a recorrer las cuentas
    while ($row_cuenta = $result_cuentas->fetch_assoc()) {
        if ($row_cuenta['tipo'] == 'Crédito') { // Solo mostrar tarjetas de crédito
            $numero_tarjeta = $row_cuenta['numero_tarjeta'];
            $banco = $row_cuenta['banco'];
            $limite_credito = number_format($row_cuenta['limite_credito'], 2);
            $saldo_disponible = number_format($row_cuenta['saldo_disponible'], 2);
            $fecha_corte = $row_cuenta['fecha_corte'];
            $fecha_pago = $row_cuenta['fecha_pago'];

            // Determinar el estado de pagos
            if ($fecha_pago < $fecha_actual) {
                $estado_pago = 'Vencido';
            } elseif ($fecha_pago == $fecha_actual) {
                $estado_pago = 'Al Día';
            } else {
                $estado_pago = 'Próximo a Vencer';
            }
            ?>
            <tr>
                <td><?php echo $numero_tarjeta; ?></td>
                <td><?php echo $banco; ?></td>
                <td>$<?php echo $limite_credito; ?></td>
                <td>$<?php echo $saldo_disponible; ?></td>
                <td><?php echo $fecha_corte; ?></td>
                <td><?php echo $fecha_pago; ?></td>
                <td><?php echo $estado_pago; ?></td>
            </tr>
            <?php
        }
    }
    ?>
    </table>
    
    <?php
} else {
    // No hay cuentas, mostramos el formulario para agregar una cuenta
    ?>
    <p>Aún no tienes cuentas agregadas. ¿Deseas agregar una? <a href="addcuenta.php">Agregar</a></p>
    <?php
}

// Resumen de Deudas y Pagos Pendientes
?>

<h2>Compras a Meses</h2>
<?php
if ($result_compras && $result_compras->num_rows > 0) {
    ?>
    <table>
        <tr>
            <th>Descripción</th>
            <th>Monto Total</th>
            <th>Monto Mensual</th>
            <th>Meses Restantes</th>
            <th>Fecha Inicio</th>
            <th>Fecha Fin</th>
        </tr>
    <?php
    while ($row_compra = $result_compras->fetch_assoc()) {
        ?>
        <tr>
            <td><?php echo $row_compra['descripcion']; ?></td>
            <td>$<?php echo number_format($row_compra['monto_total'], 2); ?></td>
            <td>$<?php echo number_format($row_compra['monto_mensual'], 2); ?></td>
            <td><?php echo $row_compra['meses_restantes']; ?></td>
            <td><?php echo $row_compra['fecha_inicio']; ?></td>
            <td><?php echo $row_compra['fecha_fin']; ?></td>
        </tr>
        <?php
    }
    ?>
    </table>
    <?php
} else {
    ?>
    <p>Aún no tienes compras a meses. ¿Deseas agregar una? <a href="addcompra.php">Agregar</a></p>
    <?php
}
?>
<h2>Deudas y Pagos Pendientes</h2>
<?php
// Inicializar el total de deudas y pagos pendientes
$total_deudas = 0;
$total_pagos = 0;

// Sumar las deudas pendientes de las cuentas
if ($result_cuentas && $result_cuentas->num_rows > 0) {
    // Reiniciar el puntero del resultado para volver a recorrer las cuentas
    $result_cuentas->data_seek(0);
    while ($row_cuenta = $result_cuentas->fetch_assoc()) {
        if ($row_cuenta['tipo'] == 'Crédito') {
            $total_deudas += $row_cuenta['saldo_actual']; // Sumar las deudas
        }
    }
}

// Sumar los montos mensuales de las compras a meses
if ($result_compras && $result_compras->num_rows > 0) {
    $result_compras->data_seek(0); // Reiniciar puntero de compras si es necesario
    while ($row_compra = $result_compras->fetch_assoc()) {
        $total_pagos += $row_compra['monto_mensual']; // Sumar los pagos mensuales
    }
}

// Calcular el total a pagar
$total_a_pagar = $total_deudas + $total_pagos;

// Mostrar resultados
if ($total_a_pagar > 0) {
    ?>
    <p><strong>Total de Deudas Pendientes:</strong> $<?php echo number_format($total_deudas, 2); ?></p>
    <p><strong>Total de Pagos Mensuales:</strong> $<?php echo number_format($total_pagos, 2); ?></p> <!-- Cambiado aquí -->
    <p><strong>Total a Pagar:</strong> $<?php echo number_format($total_a_pagar, 2); ?></p>
    <?php
} else {
    ?>
    <p>En este momento no tienes deudas ni pagos pendientes.</p>
    <?php
}
?>


</section>

</body>
</html>
