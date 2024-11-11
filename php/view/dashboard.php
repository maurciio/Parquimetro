<?php
session_start();

// Asegurar que la zona horaria esté correctamente configurada a Santiago
date_default_timezone_set('America/Santiago');

if (!isset($_SESSION['rut'])) {
    echo "<script>window.location.href = 'index.php';</script>";
    exit;
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

if (isset($_GET['patente'])) {
    $patente = $_GET['patente'];

    $query = "SELECT hora_ingreso, cantidad_minuto FROM estacionamientos WHERE patente = ? AND hora_salida IS NULL";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $patente);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $conn->close();

    if ($result) {
        $horaIngreso = new DateTime($result['hora_ingreso']);
        $horaActual = new DateTime("now", new DateTimeZone('America/Santiago'));
        $intervalo = $horaIngreso->diff($horaActual);
        $duracionMinutos = ($intervalo->days * 1440) + ($intervalo->h * 60) + $intervalo->i;

        // Calcular el cobro en base a la duración y el valor por minuto
        $cantidadMinuto = $result['cantidad_minuto'] ?? 20; // Valor predeterminado si no se especifica
        $cobro = $duracionMinutos * $cantidadMinuto;

        echo json_encode([
            'duracionMinutos' => $duracionMinutos,
            'cobro' => $cobro
        ]);
    }
}

include '../component/navegacion.php';

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - GeoParquímetro</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <link rel="stylesheet" href="./css/styles.css">
</head>

<body>

    <?php renderNav(); ?>

    <div class="container-fluid">
        <!-- Título del Dashboard -->
        <h2 class="text-center mb-4">Dashboard - GeoParquímetro</h2>

        <div class="row">
            <!-- Sección de Vehículos en Estacionamiento -->
            <div class="col-lg-8">
                <div class="table-responsive mb-4">
                    <table class="table table-hover align-middle">
                        <?php
                        include '../conexion.php';
                        $limit = 10;
                        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
                        $offset = ($page - 1) * $limit;

                        // Consulta de datos
                        $query = "SELECT * FROM estacionamientos LIMIT $limit OFFSET $offset";
                        $result = $conn->query($query);

                        if ($result) {
                            echo '<thead class="table-light">
                            <tr>
                                <th>Patente</th>
                                <th>Operador RUT</th>
                                <th>Hora de Ingreso</th>
                                <th>Hora de Salida</th>
                                <th>Duración (min)</th>
                                <th>Cantidad por Minuto ($)</th>
                                <th>Cobro Total ($)</th>
                                <th>Día Estacionado</th>
                            </tr>
                            </thead><tbody id="tablaCuerpo">';

                            while ($row = $result->fetch_assoc()) {
                                echo '<tr>';
                                echo '<td>' . htmlspecialchars($row['patente']) . '</td>';
                                echo '<td>' . htmlspecialchars($row['operador_rut']) . '</td>';
                                echo '<td>' . date('H:i', strtotime($row['hora_ingreso'])) . '</td>';
                                echo '<td>' . ($row['hora_salida'] ? date('H:i', strtotime($row['hora_salida'])) : 'Estacionado') . '</td>';

                                // Calcular duración en minutos
                                $duracionMinutos = 0;
                                $horaIngreso = new DateTime($row['hora_ingreso']);

                                if ($row['hora_salida']) {
                                    // Si existe una hora de salida, calcular la duración desde la hora de ingreso hasta la hora de salida
                                    $horaSalida = new DateTime($row['hora_salida']);
                                    $intervalo = $horaIngreso->diff($horaSalida);
                                } else {
                                    // Si no existe una hora de salida, calcular la duración desde la hora de ingreso hasta el momento actual
                                    $horaActual = new DateTime("now", new DateTimeZone('America/Santiago'));
                                    $intervalo = $horaIngreso->diff($horaActual);
                                }

                                // Convertir el intervalo a minutos correctamente
                                $duracionMinutos = ($intervalo->days * 1440) + ($intervalo->h * 60) + $intervalo->i;
                                echo '<td id="duracion">' . $duracionMinutos . ' min</td>';

                                // Definir la cantidad por minuto, usando un valor predeterminado si está vacío
                                $cantidadPorMinuto = $row['cantidad_minuto'];
                                echo '<td>' . $cantidadPorMinuto . '</td>';

                                // Calcular el cobro total basado en la duración
                                $cobroCalculado = $duracionMinutos * $cantidadPorMinuto;
                                echo '<td id="cobro">' . $cobroCalculado . '</td>';

                                echo '<td>' . date('d-m-Y', strtotime($row['hora_ingreso'])) . '</td>';
                                echo '</tr>';
                            }
                            echo '</tbody>';
                        } else {
                            echo "<tr><td colspan='8'>Error en la consulta.</td></tr>";
                        }


                        // Calcular la paginación
                        $total_query = "SELECT COUNT(*) AS total FROM estacionamientos";
                        $total_result = $conn->query($total_query);
                        $total_rows = $total_result->fetch_assoc()['total'];
                        $total_pages = ceil($total_rows / $limit);
                        $conn->close();
                        ?>
                    </table>

                    <!-- Paginación -->
                    <nav>
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                </div>
            </div>

            <?php
            include '../conexion.php';
            date_default_timezone_set('America/Santiago');
            $fechaHoy = date('Y-m-d');

            // Cálculo de ingresos diarios
            $queryDiario = "SELECT SUM(
                IF(
                    hora_salida IS NULL, 
                    TIMESTAMPDIFF(MINUTE, hora_ingreso, NOW()) * cantidad_minuto, 
                    cobro
                )
            ) AS total_diario
            FROM estacionamientos
            WHERE DATE(hora_ingreso) = CURDATE();";
            $stmtDiario = $conn->prepare($queryDiario);
            $stmtDiario->execute();
            $rowDiario = $stmtDiario->get_result()->fetch_assoc();
            $totalDiario = $rowDiario['total_diario'] ?? 0;
            $stmtDiario->close();

            // Cálculo de ingresos semanales
            $querySemanal = "SELECT SUM(
    IF(
        hora_salida IS NULL, 
        TIMESTAMPDIFF(MINUTE, hora_ingreso, NOW()) * COALESCE(cantidad_minuto, cobro), 
        cobro
    )
) AS total_semanal
FROM estacionamientos
WHERE YEARWEEK(hora_ingreso, 1) = YEARWEEK(CURDATE(), 1)";
            $stmtSemanal = $conn->prepare($querySemanal);
            $stmtSemanal->execute();
            $rowSemanal = $stmtSemanal->get_result()->fetch_assoc();
            $totalSemanal = $rowSemanal['total_semanal'] ?? 0;
            $stmtSemanal->close();

            // Cálculo de ingresos anuales
            $queryAnual = "SELECT SUM(
    IF(
        hora_salida IS NULL, 
        TIMESTAMPDIFF(MINUTE, hora_ingreso, NOW()) * COALESCE(cantidad_minuto, cobro), 
        cobro
    )
) AS total_anual
FROM estacionamientos
WHERE YEAR(hora_ingreso) = YEAR(CURDATE())";
            $stmtAnual = $conn->prepare($queryAnual);
            $stmtAnual->execute();
            $rowAnual = $stmtAnual->get_result()->fetch_assoc();
            $totalAnual = $rowAnual['total_anual'] ?? 0;
            $stmtAnual->close();

            $conn->close();
            ?>

            <!-- Mostrar los valores en las tarjetas -->
            <div class="col-lg-4">
                <div class="row g-4">
                    <div class="col-6">
                        <div class="card stats-card text-center shadow-sm border-0">
                            <div class="card-body">
                                <i class="fas fa-users text-primary mb-2" style="font-size: 1.5rem;"></i>
                                <p class="mb-1">Operarios Activos</p>
                                <h5 class="card-title">1</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card stats-card text-center shadow-sm border-0">
                            <div class="card-body">
                                <i class="fas fa-calendar-day text-primary mb-2" style="font-size: 1.5rem;"></i>
                                <p class="mb-1">Ingresos Diarios</p>
                                <h5 class="card-title">$ <?php echo number_format($totalDiario, 0, ',', '.'); ?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card stats-card text-center shadow-sm border-0">
                            <div class="card-body">
                                <i class="fas fa-calendar-week text-primary mb-2" style="font-size: 1.5rem;"></i>
                                <p class="mb-1">Ingresos Semanales</p>
                                <h5 class="card-title">$ <?php echo number_format($totalSemanal, 0, ',', '.'); ?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card stats-card text-center shadow-sm border-0">
                            <div class="card-body">
                                <i class="fas fa-calendar-check text-primary mb-2" style="font-size: 1.5rem;"></i>
                                <p class="mb-1">Ingresos Anuales</p>
                                <h5 class="card-title">$ <?php echo number_format($totalAnual, 0, ',', '.'); ?></h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        // Función para actualizar la tabla
        function actualizarTabla() {
            fetch('../data/estacionamiento/update_data.php')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById("tablaCuerpo");
                    tbody.innerHTML = ""; // Limpiar el contenido actual

                    // Recorrer los datos y agregar filas a la tabla
                    data.forEach(item => {
                        const row = document.createElement("tr");

                        row.innerHTML = `
                    <td>${item.patente}</td>
                    <td>${item.operador_rut}</td>
                    <td>${new Date(item.hora_ingreso).toLocaleTimeString()}</td>
                    <td>${item.duracion} min</td>
                    <td>${item.cantidad_minuto}</td>
                    <td>$ ${item.cobro.toLocaleString()}</td>
                `;
                        tbody.appendChild(row);
                    });
                })
                .catch(error => console.error("Error al actualizar la tabla:", error));
        }

        // Llamar a la función cada minuto
        setInterval(actualizarTabla, 1000); // 60000 ms = 1 minuto

        // Llamada inicial para actualizar inmediatamente
        actualizarTabla();
    </script>
</body>

</html>