<?php
$servername = "localhost";
$username = "root";
$password = "leica666";
$dbname = "parquimetros";

// Crear conexión y capturar errores
try {
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    // Verifica la conexión
    if (!$conn) {
        throw new Exception("Error al conectar a la base de datos.");
    }
} catch (Exception $e) {
    // Si falla, va a la página de error
    include './error_page.php';
    exit();
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ./index.php');
    exit;
}
