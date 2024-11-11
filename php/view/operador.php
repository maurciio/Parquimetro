<?php
session_start();

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

if (!isset($_SESSION['rut'])) {
    echo "<script>window.location.href = 'index.php';</script>";
    exit;
}

include '../component/navegacion.php';
include '../conexion.php';

date_default_timezone_set('America/Santiago');

$mensaje = '';
$patente = '';
$tarifa = $_POST['tarifa'] ?? 20; // Usa la tarifa ingresada o $20 si no se especifica

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verificar'])) {
    $patente = $_POST['patente'];
    $stmt = $conn->prepare("SELECT * FROM estacionamientos WHERE patente = ? AND hora_salida IS NULL");
    $stmt->bind_param("s", $patente);
    $stmt->execute();
    $result = $stmt->get_result();
    $vehiculo = $result->fetch_assoc();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['registrar_entrada'])) {
    $patente = $_POST['patente'];
    $horaIngreso = date('Y-m-d H:i:s');
    $operadorRut = $_SESSION['rut'];
    $cantidadMinuto = $_POST['tarifa'] ?? $tarifa;

    if (empty($operadorRut)) {
        $mensaje = "Error: El rut del operador no está disponible. Asegúrate de haber iniciado sesión.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM estacionamientos WHERE patente = ? AND hora_salida IS NULL");
        $stmt->bind_param("s", $patente);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $mensaje = "Error: El vehículo con patente $patente ya está registrado.";
        } else {
            $stmt = $conn->prepare("INSERT INTO estacionamientos (patente, hora_ingreso, operador_rut, cantidad_minuto) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $patente, $horaIngreso, $operadorRut, $cantidadMinuto);
            if ($stmt->execute()) {
                $mensaje = "Patente $patente registrada a las $horaIngreso.";
            } else {
                $mensaje = "Error al registrar la entrada: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['registrar_salida'])) {
    $patente = $_POST['patente'];
    $horaSalida = date('Y-m-d H:i:s');
    $stmt = $conn->prepare("SELECT * FROM estacionamientos WHERE patente = ? AND hora_salida IS NULL");
    $stmt->bind_param("s", $patente);
    $stmt->execute();
    $result = $stmt->get_result();
    $vehiculo = $result->fetch_assoc();
    $stmt->close();

    if (!$vehiculo) {
        $mensaje = "Error: No se encontró el vehículo con la patente $patente o ya ha registrado su salida.";
    } else {
        $horaIngreso = new DateTime($vehiculo['hora_ingreso']);
        $cantidadMinuto = $vehiculo['cantidad_minuto'] ?? $tarifa;

        $horaSalidaDT = new DateTime($horaSalida);
        $intervalo = $horaIngreso->diff($horaSalidaDT);
        $duracion = ($intervalo->days * 1440) + ($intervalo->h * 60) + $intervalo->i;
        $cobro = $duracion * $cantidadMinuto;

        $stmt = $conn->prepare("UPDATE estacionamientos SET hora_salida = ?, duracion = ?, cobro = ? WHERE id = ?");
        $stmt->bind_param("siii", $horaSalida, $duracion, $cobro, $vehiculo['id']);

        if ($stmt->execute()) {
            $mensaje = "Salida registrada. Valor a cobrar: $cobro CLP.";
        } else {
            $mensaje = "Error al registrar la salida: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operador de Estacionamiento</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <link rel="stylesheet" href="../view/css/styles.css">
</head>
<body>

    <?php renderNav(); ?>

    <div class="container d-flex justify-content-center">
        <div class="form-container">
            <h1 class="text-center mb-4"><i class="fa-solid fa-cash-register"></i> Registro de Estacionamiento</h1>

            <?php if (isset($mensaje) && $mensaje): ?>
                <div class="alert alert-info text-center">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>

            <!-- Formulario de registro -->
            <form method="POST">
                <div class="form-group mb-3">
                    <label for="patente"><h2>Ingresa Patente</h2></label>
                    <input type="text" class="form-control" id="patente" name="patente" value="<?php echo htmlspecialchars($patente); ?>" placeholder="Ej: ABCD12" required>
                </div>

                <?php if (empty($vehiculo)): ?>
                    <div class="form-group mb-3">
                        <label for="tarifa"><h2>Valor por Minuto</h2></label>
                        <input type="number" class="form-control" id="tarifa" name="tarifa" value="<?php echo htmlspecialchars($tarifa); ?>" required>
                    </div>
                <?php endif; ?>

                <div class="d-grid">
                    <button type="submit" name="verificar" class="btn btn-primary btn-wrapper">
                        <i class="fa-solid fa-list-check"></i> Verificar Patente
                    </button>
                </div>
            </form>

            <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verificar'])): ?>
                <div class="text-center mt-4">
                    <?php if (empty($vehiculo)): ?>
                        <form method="POST" class="d-inline-block">
                            <input type="hidden" name="patente" value="<?php echo htmlspecialchars($patente); ?>">
                            <input type="hidden" name="tarifa" value="<?php echo htmlspecialchars($tarifa); ?>">
                            <button type="submit" name="registrar_entrada" class="btn outline-entrada btn-wrapper">
                                <i class="fa-solid fa-arrow-right-to-bracket"></i> Registrar Entrada
                            </button>
                        </form>
                    <?php else: ?>
                        <form method="POST" class="d-inline-block">
                            <input type="hidden" name="patente" value="<?php echo htmlspecialchars($patente); ?>">
                            <button type="submit" name="registrar_salida" class="btn outline-salida btn-wrapper">
                                <i class="fa-solid fa-arrow-right-from-bracket"></i> Registrar Salida
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
