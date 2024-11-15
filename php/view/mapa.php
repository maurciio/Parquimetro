<?php
session_start();

include '../conexion.php';
include '../component/navegacion.php';

if ($_SESSION['rol'] == 'operador') {
    header('Location: operador.php');
}

?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Parquímetros</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>

    <!-- CSS y Librerías -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/styles.css"> <!-- CSS Consolidado -->

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <?php
    renderNav();
    ?>

    <div class="container-fluid">
        <div class="row mt mb">
            <div class="col-10">
                <div class="map-container" id="map"></div>
            </div>
            <div class="col-2">
                <div class="list-group">
                    <div class="list-group-item bg-success text-white" aria-current="true">
                        <strong>Usuarios Activos</strong>
                    </div>
                    <div class="list-group-item">
                        <span class="fw-bold">Juan Pérez</span><br>
                        <small>Última ubicación: Av. Principal 123</small>
                    </div>
                    <div class="list-group-item">
                        <span class="fw-bold">María López</span><br>
                        <small>Última ubicación: Calle 45 #789</small>
                    </div>
                    <div class="list-group-item">
                        <span class="fw-bold">Carlos Gómez</span><br>
                        <small>Última ubicación: Plaza Central</small>
                    </div>
                    <div class="list-group-item">
                        <span class="fw-bold">Ana Torres</span><br>
                        <small>Última ubicación: Av. Libertad 456</small>
                    </div>
                    <div class="list-group-item">
                        <span class="fw-bold">Luis Martínez</span><br>
                        <small>Última ubicación: Parque Industrial</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    $stmt = $conn->prepare("SELECT nombre, apellido1, apellido2, latitud, longitud, rut FROM usuarios");
    $stmt->execute();
    $result = $stmt->get_result();

    $parkMeters = [];
    while ($row = $result->fetch_assoc()) { // Cambia a fetch_assoc(), que es compatible con mysqli
        $parkMeters[] = $row;
    }

    $stmt->close();
    $conn->close();
    ?>


    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

    <script>
        // Configuración del mapa
        var map = L.map('map').setView([-34.982, -71.236], 14); // Coordenadas de Curicó
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Datos de los parquímetros desde PHP
        var parkMeters = <?php echo json_encode($parkMeters); ?>;

        // Agregar los marcadores al mapa
        parkMeters.forEach(function(pm) {
            L.marker([pm.latitud, pm.longitud]).addTo(map).bindPopup('Operador: ' + pm.nombre + ' ' + pm.apellido1 + ' ' + pm.apellido2 + ' Rut: ' + pm.rut);
        });

        // Manejar el evento del botón de inicio de sesión
        document.getElementById('btnIniciarSesion').addEventListener('click', function() {
            document.getElementById('TextoInicial').style.display = 'none';
            document.getElementById('login').style.display = 'block';
        });

        <?php if (isset($_SESSION['rut'])): ?>
            document.getElementById('TextoInicial').style.display = 'none';
            document.getElementById('login').style.display = 'none';
            document.getElementById('map').style.height = "100vh"; // Expande el mapa a toda la altura del viewport
            map.invalidateSize(); // Actualiza el tamaño del mapa
            map.setView([-34.982, -71.236], 16); // Centra el mapa en las coordenadas deseadas y ajusta el zoom
        <?php endif; ?>
    </script>