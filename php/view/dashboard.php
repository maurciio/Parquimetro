<?php
session_start();
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - GeoParquímetro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/dashboard.css">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <style>
        body {
            background: linear-gradient(90deg, #C7C5F4, #776BCC);
            font-family: Arial, sans-serif;
        }
        .card-header {
            background: #6246EA;
            color: #fff;
        }
        .table-responsive {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            overflow: hidden;
        }
        .table {
            margin-bottom: 0;
            background: #ffffff;
        }
        .pagination {
            justify-content: center;
        }
        .stats-card {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: #fff;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .stats-card .card-body i {
            font-size: 24px;
        }
    </style>
</head>

<body>

    <!-- Barra de navegación -->
    <header>
        <?php include "../component/navegacion.php"; ?>
    </header>

    <!-- Contenedor principal -->
    <div class="container mt-4 mb-4">
        <div class="row">
            <!-- Tabla de Vehículos -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header text-center">
                        <h5 class="mb-0">Vehículos en Estacionamiento</h5>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table table-hover text-center align-middle">
                            <?php
                            include '../conexion.php'; // Conexión a la base de datos
                            $limit = 10;
                            $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
                            $offset = ($page - 1) * $limit;

                            // Consulta de datos
                            $query = "SELECT * FROM estacionamientos LIMIT $limit OFFSET $offset";
                            $result = $conn->query($query);

                            if ($result) {
                                echo '<thead class="table-light"><tr><th>Patente</th><th>Entrada</th><th>Salida</th><th>Minutos</th><th>Cobro ($)</th><th>Día Estacionado</th></tr></thead><tbody>';
                                while ($row = $result->fetch_assoc()) {
                                    echo '<tr>';
                                    echo '<td>' . $row['patente'] . '</td>';
                                    echo '<td>' . date('H:i', strtotime($row['hora_ingreso'])) . '</td>';
                                    echo '<td>' . ($row['hora_salida'] ? date('H:i', strtotime($row['hora_salida'])) : 'Estacionado') . '</td>';
                                    echo '<td>' . (!empty($row['duracion']) ? $row['duracion'] : '-----') . '</td>';
                                    echo '<td>' . (!empty($row['cobro']) ? $row['cobro'] : '-----') . '</td>';
                                    echo '<td>' . date('d-m-Y', strtotime($row['hora_ingreso'])) . '</td>';
                                    echo '</tr>';
                                }
                                echo '</tbody>';
                            } else {
                                echo "<tr><td colspan='6'>Error en la consulta.</td></tr>";
                            }

                            // Paginación
                            $total_query = "SELECT COUNT(*) AS total FROM estacionamientos";
                            $total_result = $conn->query($total_query);
                            $total_rows = $total_result->fetch_assoc()['total'];
                            $total_pages = ceil($total_rows / $limit);

                            $conn->close();
                            ?>
                        </table>

                        <!-- Paginación -->
                        <nav>
                            <ul class="pagination pagination-sm">
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>

            <?php
            // Obtención de ingresos diarios, semanales y mensuales
            include '../conexion.php';
            date_default_timezone_set('America/Santiago');
            $fechaHoy = date('Y-m-d');

            $queryDiario = "SELECT SUM(cobro) AS total_diario FROM estacionamientos WHERE DATE(hora_ingreso) = ?";
            $stmtDiario = $conn->prepare($queryDiario);
            $stmtDiario->bind_param("s", $fechaHoy);
            $stmtDiario->execute();
            $rowDiario = $stmtDiario->get_result()->fetch_assoc();
            $totalDiario = $rowDiario['total_diario'] ?? 0;

            $querySemanal = "SELECT SUM(cobro) AS total_semanal FROM estacionamientos WHERE YEARWEEK(hora_ingreso, 1) = YEARWEEK(CURDATE(), 1)";
            $totalSemanal = $conn->query($querySemanal)->fetch_assoc()['total_semanal'] ?? 0;

            $queryMensual = "SELECT SUM(cobro) AS total_mensual FROM estacionamientos WHERE MONTH(hora_ingreso) = MONTH(CURDATE()) AND YEAR(hora_ingreso) = YEAR(CURDATE())";
            $totalMensual = $conn->query($queryMensual)->fetch_assoc()['total_mensual'] ?? 0;

            $conn->close();
            ?>

            <!-- Tarjetas con estadísticas -->
            <div class="col-lg-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card stats-card text-center p-3">
                            <div class="card-body">
                                <p><i class="fas fa-users"></i> Operarios Activos</p>
                                <h5 class="card-title">1</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card stats-card text-center p-3">
                            <div class="card-body">
                                <p><i class="fas fa-calendar-day"></i> Ingresos Diarios</p>
                                <h5 class="card-title">$ <?php echo number_format($totalDiario, 0, ',', '.'); ?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card stats-card text-center p-3">
                            <div class="card-body">
                                <p><i class="fas fa-calendar-week"></i> Ingresos Semanales</p>
                                <h5 class="card-title">$ <?php echo number_format($totalSemanal, 0, ',', '.'); ?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card stats-card text-center p-3">
                            <div class="card-body">
                                <p><i class="fas fa-calendar-check"></i> Ingresos Mensuales</p>
                                <h5 class="card-title">$ <?php echo number_format($totalMensual, 0, ',', '.'); ?></h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
