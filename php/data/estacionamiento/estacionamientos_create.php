<?php
require '../../conexion.php';

$patente = 'ABC1234';
$operador_rut = '12345678-9';
$hora_ingreso = date('Y-m-d H:i:s');

$sql = "INSERT INTO estacionamientos (patente, operador_rut, hora_ingreso) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $patente, $operador_rut, $hora_ingreso);

if ($stmt->execute()) {
    echo "Estacionamiento registrado exitosamente.";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?> 