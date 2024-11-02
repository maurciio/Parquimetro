<?php
require '../../conexion.php';

$rut = $_POST['rut'];
$nombre = $_POST['nombre'];
$apellido1 = $_POST['apellido1'];
$apellido2 = $_POST['apellido2'];
$contraseña = password_hash($_POST['contraseña'], PASSWORD_BCRYPT);
$rol = $_POST['rol'];
$numero = $_POST['telefono'];
$activo = true;

$sql = "INSERT INTO usuarios (rut, nombre, apellido1, apellido2, contraseña, rol, numero, activo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssii", $rut, $nombre, $apellido1, $apellido2, $contraseña, $rol, $numero, $activo);

if ($stmt->execute()) {
    echo "Usuario creado exitosamente.";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
