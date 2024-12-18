<?php
session_start();

include '../conexion.php';
include '../component/navegacion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rut = $_POST['rut'];
    $contraseña = hash('sha256', $_POST['contraseña']);

    // Consulta para obtener el usuario de la base de datos
    $stmt = $conn->prepare("SELECT rut, rol, contraseña, estado, login FROM usuarios WHERE rut = ?");
    $stmt->bind_param("s", $rut);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Verifica si el usuario existe y si la contraseña es correcta
    if ($user && $user['contraseña'] === $contraseña && $user['estado'] === 1 && $user['login'] === 0) {
        // Actualizar el campo 'login' a 1 en la base de datos
        $updateLogin = $conn->prepare("UPDATE usuarios SET login = ? WHERE rut = ?");
        $loginStatus = 1; // Establece login a 1 para indicar que el usuario está logueado
        $updateLogin->bind_param("is", $loginStatus, $rut);
        $updateLogin->execute();
        $updateLogin->close();

        // Configurar la sesión del usuario
        $_SESSION['rut'] = $user['rut'];
        $_SESSION['rol'] = $user['rol'];
        $_SESSION['login'] = 1;

        // Redirigir según el rol del usuario
        if ($_SESSION['rol'] == 'administrador') {
            header('Location: dashboard.php');
        } else if ($_SESSION['rol'] == 'operador') {
            header('Location: operador.php');
        }
        exit;
    } else if (!$user) {
        $error = "Usuario no existe.";
    } else {
        $error = "Datos incorrectos o usuario ya está logueado.";
    }
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
</body>

</html>