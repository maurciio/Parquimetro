<?php
session_start();
include '../conexion.php'; 

// Verifica si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rut = $_POST['rut'];
    $contraseña = hash('sha256', $_POST['contraseña']); // Encripta la contraseña con SHA-256

    // Consulta para obtener el usuario de la base de datos
    $stmt = $conn->prepare("SELECT rut, rol, contraseña, estado FROM usuarios WHERE rut = ?");
    $stmt->bind_param("s", $rut);
    $stmt->execute();
    $result = $stmt->get_result();

    $user = $result->fetch_assoc();

    // Verifica si el usuario existe y si la contraseña es correcta
    if ($user && $user['contraseña'] === $contraseña && $user['estado'] === 1) {
        $_SESSION['rut'] = $user['rut'];
        $_SESSION['role'] = $user['rol'];

        if ($_SESSION['role'] = 'administrador') {
            header('Location: dashboard.php');
        }else{
            header('Location: operador.php');
        }
        exit;
    } else if(!$user){
        $error = "Usuario no existe.";
    }else {
        $error = "Datos incorrectos";
    }
}
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Parquímetros</title>

    <!-- CSS y Librerías -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/styles.css"> <!-- CSS Consolidado -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <?php include '../component/navegacion.php'; ?>

    <!-- Sección de bienvenida con botón para abrir el modal de inicio de sesión -->
    <section class="hero-section d-flex flex-column align-items-center text-black" style="height: 100vh; padding-top: 50px;">
        <div class="container text-center" id="TextoInicial">
            <div class="overlay d-flex justify-content-center align-items-center">
                <div class="text-overlay">
                    <h1 class="display-3 fw-bold mb-4 animate__animated animate__fadeIn" style="color: white;">GeoParquímetro</h1>
                    <p class="lead mb-4 animate__animated animate__fadeIn animate__delay-1s" style="color: white">Monitorea tus ingresos en tiempo real y mantén controlado a tus parquímetros</p>
                    <button type="button" class="btn mb-3 btn-primary btn-lg animate__animated animate__fadeIn animate__delay-2s" data-bs-toggle="modal" data-bs-target="#loginModal">Iniciar Sesión</button>
                </div>
            </div>
        </div>

        <div class="container" style="margin: 20px;">
            <div class="row centered mt mb">
                <div class="map-container" id="map"></div>
            </div>
        </div>

        <?php if (isset($error)): ?>
            <div id="error-message" class="alert alert-danger text-center">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- Modal de Inicio de Sesión -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content shadow-lg border-0 rounded-3">
                <!-- Encabezado del Modal -->
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-uppercase fw-bold mx-auto" id="loginModalLabel">Iniciar Sesión</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <!-- Cuerpo del Modal -->
                <div class="modal-body">
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">

                        <div class="mb-3">
                            <label for="loginName" class="form-label fw-semibold">Rut del usuario</label>
                            <input type="text" id="loginName" class="form-control" name="rut" required placeholder="Ingrese el rut del usuario" autocomplete="off" />
                        </div>
                        <div class="mb-3">
                            <label for="loginPassword" class="form-label fw-semibold">Contraseña</label>
                            <input type="password" id="loginPassword" class="form-control" name="contraseña" required placeholder="Ingrese su contraseña" autocomplete="off" />
                        </div>

                        <!-- Botón de envío -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Ingresar</button>
                        </div>

                        <!-- Enlace para recuperar contraseña -->
                        <div class="text-center mt-3">
                            <a href="recovery-password.php" class="text-primary small" data-bs-dismiss="modal">
                                ¿Olvidó su contraseña?
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php
    $query = "SELECT nombre, apellido1, apellido2, latitud, longitud, rut FROM usuarios";
    $stmt = $conn->prepare($query);
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
</body>

</html>