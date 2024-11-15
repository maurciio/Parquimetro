<?php
require '../../conexion.php';

$sql = "SELECT * FROM usuarios";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['rut']}</td>";
        echo "<td>{$row['nombre']}</td>";
        echo "<td>{$row['apellido1']}</td>";
        echo "<td>{$row['apellido2']}</td>";
        echo "<td>{$row['numero']}</td>";
        echo "<td>{$row['rol']}</td>";
        echo "<td>" . ($row['activo'] ? 'Activo' : 'Inactivo') . "</td>";
        echo "<td>
                <button class='btn btn-warning btnEditar' data-usuario='" . json_encode($row) . "'>Editar</button>
                <button class='btn btn-danger btnDeshabilitar' data-rut='{$row['rut']}'>Deshabilitar</button>
              </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='8'>No hay usuarios registrados.</td></tr>";
}

?>
