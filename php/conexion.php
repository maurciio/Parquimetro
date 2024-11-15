<?php
// Configuración de la conexión
$servername = "localhost";
$username = "root";
$password = "leica666";
$dbname = "parquimetros";

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}

// Opcionalmente, puedes definir una función para usar esta conexión en caso de que necesites centralizarla más
function getDBConnection()
{
    global $conn;
    return $conn;
}



// Manejo de logout
function cerrarSesion()
{
    include './conexion.php';
    global $conn;

    if (isset($_SESSION['rut'])) {
        $rut = $_SESSION['rut'];

        // Actualizar el campo 'login' a 0 en la base de datos
        $updateLogin = $conn->prepare("UPDATE usuarios SET login = ? WHERE rut = ?");
        $loginStatus = 0;
        $updateLogin->bind_param("is", $loginStatus, $rut);

        if ($updateLogin->execute()) {
            session_destroy(); // Destruir la sesión
            header('Location: ./index.php');
            exit();
        } else {
            echo "<script>alert('Error al actualizar el estado de login: " . $updateLogin->error . "');</script>";
        }

        $updateLogin->close();
    }
}

// Llama a `cerrarSesion()` si se recibe `logout=true` en la URL
if (isset($_GET['logout'])) {
    cerrarSesion();
}
