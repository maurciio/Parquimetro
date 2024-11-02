<?php
require '../../conexion.php';

$id = 1; // ID del registro a eliminar

$sql = "DELETE FROM estacionamientos WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "Estacionamiento eliminado exitosamente.";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?> 