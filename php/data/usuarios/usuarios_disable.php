<?php
require '../../conexion.php';

$rut = $_POST['rut'];

$sql = "UPDATE usuarios SET estado = 0 WHERE rut = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $rut);

if ($stmt->execute()) {
    header('Location: ../../view/usuarios.php');
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
