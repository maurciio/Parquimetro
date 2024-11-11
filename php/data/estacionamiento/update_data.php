<?php
include '../conexion.php';
date_default_timezone_set('America/Santiago');

// Consulta para obtener todos los registros
$query = "SELECT id, patente, operador_rut, hora_ingreso, hora_salida, duracion, cobro, cantidad_minuto 
          FROM estacionamientos";
$result = $conn->query($query);

$datos = [];

while ($row = $result->fetch_assoc()) {
    $horaIngreso = new DateTime($row['hora_ingreso']);
    $cantidadMinuto = $row['cantidad_minuto'] ?? 20;

    if ($row['hora_salida']) {
        // Si el registro tiene una hora de salida, usa el valor almacenado
        $duracionMinutos = $row['duracion'];
        $cobro = $row['cobro'];
        $horaSalida = $row['hora_salida'];
    } else {
        // Si no tiene hora de salida, calcula la duración hasta el momento actual
        $horaActual = new DateTime();
        $intervalo = $horaIngreso->diff($horaActual);
        $duracionMinutos = ($intervalo->days * 1440) + ($intervalo->h * 60) + $intervalo->i;
        $cobro = $duracionMinutos * $cantidadMinuto;
        $horaSalida = 'Estacionado';
    }

    // Añadir los datos actualizados
    $datos[] = [
        'patente' => $row['patente'],
        'operador_rut' => $row['operador_rut'],
        'hora_ingreso' => $row['hora_ingreso'],
        'hora_salida' => $horaSalida,
        'duracion' => $duracionMinutos,
        'cantidad_minuto' => $cantidadMinuto,
        'cobro' => $cobro
    ];
}

$conn->close();

// Devolver los datos en formato JSON
echo json_encode($datos);
?>