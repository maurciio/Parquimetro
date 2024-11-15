<?php
include '../../conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rut = $_POST['rut'];
    $contraseña = hash('sha256', $_POST['contraseña']); // Cifrar la contraseña
    $nombre = $_POST['nombre'];
    $apellido1 = $_POST['apellido1'];
    $apellido2 = $_POST['apellido2'];
    $telefono = $_POST['telefono'];
    $lat = floatval($_POST['latUsuario']);
    $long = floatval($_POST['longUsuario']);
    $rol = $_POST['rol'];

    $stmt = $conn->prepare("INSERT INTO usuarios (rut, contraseña, nombre, apellido1, apellido2, numero, latitud, longitud, rol) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssdds", $rut, $contraseña, $nombre, $apellido1, $apellido2, $telefono, $lat, $long, $rol);

    if ($stmt->execute()) {
        echo "Usuario creado exitosamente.";
    } else {
        echo "Error al crear el usuario: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>


