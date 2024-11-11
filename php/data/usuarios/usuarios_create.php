<?php
include '../../conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rut = $_POST['rut'];
    $contraseña = hash('sha256', $_POST['contraseña']);  // Usar SHA-256 para consistencia
    $nombre = $_POST['nombre'];
    $apellido1 = $_POST['apellido1'];
    $apellido2 = $_POST['apellido2'];
    $telefono = $_POST['telefono'];
    $rol = $_POST['rol'];

    $stmt = $conn->prepare("INSERT INTO usuarios (rut, contraseña, nombre, apellido1, apellido2, numero, rol) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $rut, $contraseña, $nombre, $apellido1, $apellido2, $telefono, $rol);

    if ($stmt->execute()) {
        echo "Usuario creado exitosamente";
    } else {
        echo "Error al crear el usuario: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}

