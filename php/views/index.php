<?php
session_start();

include ("conexion.php");

// Verifica si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rut = $_POST['rut'];
    $contraseña = $_POST['contraseña'];

    // Consulta para obtener el usuario de la base de datos
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE rut = :rut");
    $stmt->bindParam(':rut', $rut);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifica si el usuario existe y la contraseña es correcta
    if ($user && $user['contraseña'] === hash('sha256', $contraseña)) {
        $_SESSION['rut'] = $user['rut'];
        $_SESSION['role'] = $user['rol'];

        
        // Redirecciona según el rol del usuario, pero en este caso, solo actualizamos la página
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;   
    } else {
        // Si los datos no son válidos, muestra un mensaje de error
        echo "<div class='alert alert-danger text-center'> Usuario o contraseña incorrectos.</div>";
    }
}
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
    <title>Login - Sistema de Parquímetros</title>
    <link rel="stylesheet" href="">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/login.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    
</head>
<body>
        <header>
            <!-- barra de navegación -->
            <?php include "navegacion.php"; ?>
        </header> 

    <section class="hero-section d-flex flex-column align-items-center text-black" style="height: 100vh; padding-top: 50px;">
            <!-- Pantalla inicial -->
            <div class="container text-center" id="TextoInicial">
                <div class="overlay d-flex justify-content-center align-items-center" >
                    <div class="text-overlay" >
                        <h1 class="display-3 fw-bold mb-4 animate__animated animate__fadeIn" style="color: white;">GeoParquímetro</h1>
                        <p class="lead mb-4 animate__animated animate__fadeIn animate__delay-1s" style="color: white">Monitorea tus ingresos en tiempo real y mantén controlado a tus parquímetros</p>
                        <button id="btnIniciarSesion" class="btn mb-3 btn-primary btn-lg animate__animated animate__fadeIn animate__delay-2s">Iniciar Sesión</button>
                    </div>
                </div>
            </div>
            
            <div id="login" class="login mt-5" style="display: none; margin-left:20px;">
                    <div class="screen">
                        <div class="screen__content">
                            <form method="POST"class="login">
                            <div id="error-message" class="alert alert-danger text-center" style="display: none;"></div>
                                <div class="login__field">
                                    <i class="login__icon fas fa-user"></i>
                                    <input type="text" class="login__input" id="rut" name="rut" placeholder="Usuario" required>
                                </div>
                                <div class="login__field">
                                    <i class="login__icon fas fa-lock"></i>
                                    <input type="password" class="login__input" id="contraseña" name="contraseña" placeholder="Contraseña" required>
                                </div>
                                <button class="button login__submit">
                                    <span class="button__text">Iniciar Sesion</span>
                                    <i class="button__icon fas fa-chevron-right"></i>
                                </button>				
                            </form>
                           
                        </div>
                        <div class="screen__background">
                            <span class="screen__background__shape screen__background__shape4"></span>
                            <span class="screen__background__shape screen__background__shape3"></span>		
                            <span class="screen__background__shape screen__background__shape2"></span>
                            <span class="screen__background__shape screen__background__shape1"></span>
                        </div>		
                    </div>
                </div>

        

        <!-- Mapa -->
        <div class="Q container" style="margin-left:20px; margin-right:20px; margin-top:20px; ">
            <div class="row centered mt mb">
                <div class="map-container" id="map"></div>
            </div>
        </div>
    </section>

    <?php
    // Incluye el archivo de conexión a la base de datos
    include 'conexion.php';

    // Consulta para obtener los datos de los parquímetros
    $query = "SELECT nombre, apellido1, apellido2, latitud, longitud, rut FROM usuarios";
    $stmt = $conn->prepare($query);
    $stmt->execute();

    // Almacenar los resultados
    $parkMeters = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $parkMeters[] = $row; // Almacena cada fila en el array
    }

    // Cerrar la conexión a la base de datos
    $conn = null;
    ?>
    <!-- Visualización del Mapa -->
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
            L.marker([pm.latitud, pm.longitud]).addTo(map).bindPopup('Operador: '+ pm.nombre + ' ' + pm.apellido1 + ' ' + pm.apellido2 + ' Rut: ' + pm.rut );
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
    <!-- <script>
        setInterval(function() {
            location.reload();
        }, 6000);
    </script> -->

</body>
</html>
