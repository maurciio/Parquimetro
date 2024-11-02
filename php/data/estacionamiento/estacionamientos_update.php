<?php
require '../../conexion.php';

$id = 1; // ID del registro a actualizar
$hora_salida = date('Y-m-d H:i:s');
$duracion = 120; // Duración en minutos
$cobro = 5000; // Cobro en la moneda local

$sql = "UPDATE estacionamientos SET hora_salida=?, duracion=?, cobro=? WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("siii", $hora_salida, $duracion, $cobro, $id);

if ($stmt->execute()) {
    echo "Estacionamiento actualizado exitosamente.";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?> 