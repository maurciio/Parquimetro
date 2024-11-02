<?php
require '../../conexion.php';

$sql = "SELECT * FROM estacionamientos";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "ID: " . $row["id"]. " - Patente: " . $row["patente"]. " - Operador: " . $row["operador_rut"]. " - Hora Ingreso: " . $row["hora_ingreso"]. "<br>";
    }
} else {
    echo "0 resultados";
}

$conn->close();
?> 