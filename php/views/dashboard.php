<?php
session_start();
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
} ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title class="titulo">Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <!-- Include Bootstrap CSS and Animate.css -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/dashboard.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


</head>
<header>
    <!-- barra de navegación -->
    <?php include "navegacion.php"; ?>
</header>

<body>


    <div class="d-flex flex-column min-vh-100">
        <main class="flex-grow-1 p-4">
            <div class="container">
                <div class="row">
                    <!-- Table Card -->
                    <div class="col-md-8 dashboard">
                        <div class="card-dashboard border-light shadow-sm ">
                            <div class="dashboard-header bg-primary text-light">
                                <h5 class="dashboard-title mb-0 text-center">Vehiculos en Estacionamiento</h5>
                            </div>
                            <div class="dashboard-body">

                                <table class="table table-hover ">
                                    <?php
                                    include 'conexion.php'; // Conexión a la base de datos

                                    $limit = 10; // Número de filas por página
                                    $page = isset($_GET['page']) ? intval($_GET['page']) : 1; // Página actual
                                    $offset = ($page - 1) * $limit;

                                    // Consulta para obtener los registros de la tabla estacionamientos con límite y offset
                                    $query = "SELECT * FROM estacionamientos LIMIT $limit OFFSET $offset";
                                    $result = $conn->query($query);

                                    // Verificar si la consulta fue exitosa
                                    if ($result === false) {
                                        // Mostrar el error si la consulta falla
                                        echo "Error en la consulta: " . $e->getMessage();
                                    } else {
                                        // Si la consulta es exitosa, mostrar los resultados
                                        echo '<h2 class="text-center mt-2"><i class="fa-solid fa-table-columns"></i> Listado Vehículos</h2>';
                                        echo '<table class="table table-hover  text-center" >';
                                        echo '<thead><tr><th>Patente</th><th>Entrada</th><th>Salida</th><th>Minutos</th><th>Cobro ($)</th><th>Dia Estacionado</th></tr></thead>';
                                        echo '<tbody>';
                                        // Recorrer y mostrar los resultados
                                        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                            echo '<tr>';
                                            echo '<td>' . $row['patente'] . '</td>';
                                            
                                            // Formatear la hora de ingreso
                                            $horaIngresoFormateada = date('H:i', strtotime($row['hora_ingreso']));
                                            echo '<td>' . $horaIngresoFormateada . '</td>';
                                            
                                            // Formatear la salida para mostrar solo la hora
                                            $horaSalidaFormateada = date('H:i', strtotime($row['hora_salida']));
                                            echo '<td>' . ($row['hora_salida'] ? $horaSalidaFormateada : 'Estacionado') . '</td>';                                            
                                            echo '<td>' . (!empty($row['duracion']) ? $row['duracion'] : '-----') . '</td>';
                                            echo '<td>' . (!empty($row['cobro']) ? $row['cobro'] : '-----') . '</td>';
                                            echo '<td>' . date('d-m-Y ', strtotime($row['hora_ingreso'])) . '</td>';
                                            echo '</tr>';
                                        }
                                        echo '</tbody></table>';
                                    }

                                    // Obtener el total de filas para la paginación
                                    $total_query = "SELECT COUNT(*) AS total FROM estacionamientos";
                                    $total_result = $conn->query($total_query);
                                    $total_row = $total_result->fetch(PDO::FETCH_ASSOC);
                                    $total_rows = $total_row['total'];
                                    $total_pages = ceil($total_rows / $limit);

                                    $conn = null;
                                    ?>

                                    <nav>
                                        <ul class="pagination">
                                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                                <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                                </li>
                                            <?php endfor; ?>
                                        </ul>
                                    </nav>


                                    </tbody>
                                </table>
                                <!-- Pagination -->


                            </div>
                        </div>
                    </div>

                    <?php
                    include 'conexion.php';

                    // Establecer la zona horaria a Chile
                    date_default_timezone_set('America/Santiago');

                    // Obtener la fecha actual
                    $fechaHoy = date('Y-m-d');

                    // Consulta para los cobros diarios
                    $queryDiario = "SELECT SUM(cobro) AS total_diario FROM estacionamientos WHERE DATE(hora_ingreso) = ?";
                    $queryDiario = $conn->prepare($queryDiario);
                    $stmtDiario->bindParam(1, $fechaHoy, PDO::PARAM_STR);
                    $stmtDiario->execute();
                    $resultDiario = $stmtDiario->fetchAll(PDO::FETCH_ASSOC);
                    $rowDiario = $stmtDiario->fetch(PDO::FETCH_ASSOC);
                    $totalDiario = $rowDiario['total_diario'] ? $rowDiario['total_diario'] : 0;

                    // Ingresos Semanales
                    $query_semanal = "
                        SELECT SUM(cobro) AS total_semanal
                        FROM estacionamientos
                        WHERE YEARWEEK(hora_ingreso, 1) = YEARWEEK(CURDATE(), 1)
                        ";
                    $result_semanal = $conn->query($query_semanal);
                    $total_semanal = $result_semanal->fetch(PDO::FETCH_ASSOC)['total_semanal'];

                    // Ingresos Mensuales
                    $query_mensual = "SELECT SUM(cobro) AS total_mensual FROM estacionamientos WHERE MONTH(hora_ingreso) = MONTH(CURDATE()) AND YEAR(hora_ingreso) = YEAR(CURDATE())";
                    $result_mensual = $conn->query($query_mensual);
                    $total_mensual = $result_mensual->fetch()['total_mensual'];


                    $conn =null;
                    ?>


                    <!-- Cards on the Right -->
                    <div class="cartasderecha col-md-4">
                        <div class="row g-4">

                                           

                                       
                            <!-- Suppliers Card -->
                            <div class="col-6 ">
                                <div class="card">
                                    <div class="card-body">
                                        <p class="card-text"><i class="fa-solid fa-users"></i> Operarios Activos</p>
                                        <h5 class="card-title">1</h5>
                                    </div>
                                    <div class="card-footer text-center">
                                        <button class="btn btn-wrapper outline" data-bs-toggle="modal" data-bs-target="#infoModal">Ver</button>
                                    </div>
                                </div>
                            </div>

                        <div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="infoModalLabel">Información del Usuario</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Aquí se cargará la información dinámicamente -->
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                            <!-- Ingreso Diarios-->
                            <div class="col-6 ">
                                <div class="card">
                                    <div class="card-body">
                                        <p class="card-text"><i class="fa-solid fa-calendar-day"></i> Ingresos Diarios</p>
                                        <!-- Mostrar el total de cobros diarios -->
                                        <h5 class="card-title">$ <?php echo number_format($totalDiario, 0, ',', '.'); ?> </h5>

                                    </div>
                                    <div class="card-footer text-center">
                                        <button class="btn btn-wrapper outline ">Ver</button>
                                    </div>
                                </div>
                            </div>


                            <!-- Tarjeta para cobros semanales -->
                            <div class="col-6 ">
                                <div class="card">
                                    <div class="card-body">
                                        <p class="card-text"><i class="fa-solid fa-calendar-week"></i><I></I> Ingresos Semanales</p>
                                        <h5 class="card-title">$ <?php echo $total_semanal ?: 0; ?></h5>

                                    </div>
                                    <div class="card-footer text-center">
                                        <button class="btn btn-wrapper outline">Ver </button>
                                    </div>
                                </div>
                            </div>
                            <!-- Tarjeta para cobros Mensuales -->
                            <div class="col col-6  ">
                                <div class="card">
                                    <div class="card-body">
                                        <p class="card-text"><i class="fa-solid fa-calendar-check"></i> Ingresos Mensuales</p>
                                        <h5 class="card-title">$ <?php echo $total_mensual ?: 0; ?> </h5>
                                    </div>
                                    <div class="card-footer text-center">
                                        <button class="btn btn-wrapper outline ">Ver</button>
                                    </div>
                                </div>
                            </div>



                        </div>
                    </div>

                </div>
            </div>
        </main>
    </div>
<!-- <script>
        setInterval(function() {
            location.reload();
        }, 6000);
    </script> -->

    <!-- Bootstrap JS y Popper.js (si aún no los has incluido) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Tu script personalizado -->
<script src="js\dashboard.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>