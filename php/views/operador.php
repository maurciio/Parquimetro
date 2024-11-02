<?php
// Asegúrate de que el usuario está autenticado y tiene un rut en la sesión
session_start();
require 'conexion.php';
 // Conexión a la base de datos

// Establecer la zona horaria a Chile
date_default_timezone_set('America/Santiago');


$cobroPorMinuto = isset($_POST['tarifa']) ? (float)$_POST['tarifa'] : 20; // Tarifa predeterminada de $20 CLP por minuto o la ingresada
$mensaje = '';
$cobro = '';
$duracion = '';
$vehiculo = [];
$patente = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verificar'])) {
    $patente = $_POST['patente'];

    // Verificar si la patente ya tiene un ingreso sin salida
    $stmt = $conn->prepare("SELECT * FROM estacionamientos WHERE patente = ? AND hora_salida IS NULL");
    $stmt->bind_param("s", $patente);
    $stmt->execute();
    $result = $stmt->get_result();
    $vehiculo = $result->fetch_assoc();
}


    // Registrar entrada
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['registrar_entrada'])) {
        $patente = $_POST['patente'];
        $horaIngreso = date('Y-m-d H:i:s'); // Hora actual con fecha
        $operadorRut = $_SESSION['rut']; // Obtener el rut del operador de la sesión

        // Verificar que el operadorRut no sea nulo
        if (empty($operadorRut)) {
            $mensaje = "Error: El rut del operador no está disponible. Asegúrate de haber iniciado sesión.";
        } else {
            // Verificar si la patente ya está registrada
            $stmt = $conn->prepare("SELECT * FROM estacionamientos WHERE patente = ? AND hora_salida IS NULL");
            $stmt->bind_param("s", $patente);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $mensaje = "Error: El vehículo con patente $patente ya está registrado.";
            } else {
                // Insertar el nuevo registro en la base de datos
                $stmt = $conn->prepare("INSERT INTO estacionamientos (patente, hora_ingreso, operador_rut) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $patente, $horaIngreso, $operadorRut);
                if ($stmt->execute()) {
                    $mensaje = "Patente $patente registrada a las $horaIngreso.";
                } else {
                    $mensaje = "Error al registrar la entrada: " . $stmt->error;
                }
            }
        }
    }

    // Registrar salida
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['registrar_salida'])) {
        $patente = $_POST['patente'];
        $horaSalida = date('Y-m-d H:i:s'); // Hora actual con fecha

        // Obtener la hora de ingreso
        $stmt = $conn->prepare("SELECT * FROM estacionamientos WHERE patente = ? AND hora_salida IS NULL");
        $stmt->bind_param("s", $patente);
        $stmt->execute();
        $result = $stmt->get_result();
        $vehiculo = $result->fetch_assoc();

        // Verificar si el vehículo está registrado
        if (!$vehiculo) {
            $mensaje = "Error: No se encontró el vehículo con la patente $patente o ya ha registrado su salida.";
        } else {
            $horaIngreso = new DateTime($vehiculo['hora_ingreso']);

            // Calcular la duración y el cobro
            $horaSalidaDT = new DateTime($horaSalida);
            $intervalo = $horaIngreso->diff($horaSalidaDT);
            $duracion = ($intervalo->h * 60) + $intervalo->i; // Duración en minutos
            $cobro = $duracion * $cobroPorMinuto; // Asegúrate de definir $cobroPorMinuto

           
          // Actualizar registro de salida
            $stmt = $conn->prepare("UPDATE estacionamientos SET hora_salida = ?, duracion = ?, cobro = ? WHERE id = ?");
            $stmt->bind_param("ssii", $horaSalida, $duracion, $cobro, $vehiculo['id']);

            if ($stmt->execute()) {
                $mensaje = "Salida registrada. Valor a cobrar: $cobro CLP.";
            } else {
                $mensaje = "Error al registrar la salida: " . $stmt->error;
            }
        }
    }



// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}
?>
    


<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
    <title>Operador de Estacionamiento</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <link rel="stylesheet" href="../../css/cobro.css">
</head>
        <header>
            <!-- barra de navegación -->
            <?php include "navegacion.php"; ?>
        </header> 
<body>      
                
    <div class="container">

        <h1><i class="fa-solid fa-cash-register"></i> 
                <?php if ($mensaje): ?>
                <div class="alert alert-info mt-3">
                <?php echo $mensaje; ?>
                 </div>                 
        <?php endif; ?> Registro de Estacionamiento</h1>
                 

        <form method="POST">
            <div class="form-group outline">
                <label for="patente"><h2>Ingresa Patente</h2></label>
                <input type="text" class="form-control" id="patente" name="patente" value="<?php echo htmlspecialchars($patente); ?>" placeholder="Ej: ABCD12" required>
            </div>
            <div class="form-group outline">
                <label for="tarifa"><h2>Valor por Minuto</h2></label>
                <input type="number" class="form-control" id="tarifa" name="tarifa" value="<?php echo htmlspecialchars($cobroPorMinuto); ?>" required>
            </div>
            <!-- Contenedor para centrar el botón -->
            <div class="d-flex justify-content-center">
                <button type="submit" name="verificar" class="btn btn-wrapper outline">
                    <i class="fa-solid fa-list-check"></i> Verificar Patente
                </button>
            </div>
        </form>


        

        <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verificar'])): ?>
            <!-- Si no está registrada, mostrar botón para registrar entrada -->
            <?php if (!$vehiculo): ?>
                <form method="POST" class="d-flex  justify-content-center" >
                    <input type="hidden" name="patente" value="<?php echo htmlspecialchars($patente); ?>">
                    <button type="submit" name="registrar_entrada" class="btn btn-sm btn-wrapper outline-entrada ">Registrar Entrada</button>
                </form>
            <?php else: ?>
                <!-- Si está registrada, mostrar botón para registrar salida -->
                <form method="POST" class="d-flex  justify-content-center">
                    <input type="hidden" name="patente" value="<?php echo htmlspecialchars($patente); ?>">
                    <button type="submit" name="registrar_salida" class="btn  btn-wrapper outline-salida">Registrar Salida</button>
                </form>
                <!-- Mostrar el monto a cobrar si ya se registró la salida -->
                
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>


