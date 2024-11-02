<?php
require '../../conexion.php';

$rut = $_POST['rut'];
$nombre = $_POST['nombre'];
$apellido1 = $_POST['apellido1'];
$apellido2 = $_POST['apellido2'];
$telefono = $_POST['telefono'];
$rol = $_POST['rol'];

$sql = "UPDATE usuarios SET nombre=?, apellido1=?, apellido2=?, numero=?, rol=? WHERE rut=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", $nombre, $apellido1, $apellido2, $telefono, $rol, $rut);

if ($stmt->execute()) {
    echo "Usuario actualizado exitosamente.";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
