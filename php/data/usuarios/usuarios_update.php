<?php
include '../../conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rut = $_POST['rut'];
    $contraseña = !empty($_POST['contraseña']) ? password_hash($_POST['contraseña'], PASSWORD_DEFAULT) : null;
    $nombre = $_POST['nombre'];
    $apellido1 = $_POST['apellido1'];
    $apellido2 = $_POST['apellido2'];
    $telefono = $_POST['telefono'];
    $rol = $_POST['rol'];

    if ($contraseña) {
        $stmt = $conn->prepare("UPDATE usuarios SET contraseña = ?, nombre = ?, apellido1 = ?, apellido2 = ?, numero = ?, rol = ? WHERE rut = ?");
        $stmt->bind_param("sssssss", $contraseña, $nombre, $apellido1, $apellido2, $telefono, $rol, $rut);
    } else {
        $stmt = $conn->prepare("UPDATE usuarios SET nombre = ?, apellido1 = ?, apellido2 = ?, numero = ?, rol = ? WHERE rut = ?");
        $stmt->bind_param("ssssss", $nombre, $apellido1, $apellido2, $telefono, $rol, $rut);
    }

    if ($stmt->execute()) {
        echo "Usuario actualizado exitosamente";
    } else {
        echo "Error al actualizar el usuario: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
