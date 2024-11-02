<?php
// Asegúrate de que el usuario está autenticado y tiene un rut en la sesión
session_start();
require '../conexion.php';
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: /ParquimetroPHP/php/views/index.php ');
    exit;
}

require '../conexion.php';

$sql = "SELECT * FROM usuarios";
$result = $conn->query($sql);

$usuarios = [];



// Consulta para contar el total de usuarios
$sql = "SELECT COUNT(*) AS total FROM usuarios";
$result = $conn->query($sql);

$totalUsuarios = 0;
if ($result) {
    $row = $result->fetch_assoc();
    $totalUsuarios = $row['total'] - 1;
}


?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios de Parquímetros</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <link href="../CRUD/Usuarios.css" rel="stylesheet">
</head>

<?php include "../component/navegacion-user.php"; ?>

<body>
    <div class="container-fluid py-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow-lg">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h1 class="text-primary">
                                <i class="fas fa-parking me-2"></i>
                                Gestión de Usuarios de Parquímetros
                            </h1>
                            <button id="btnNuevoUsuario" class="btn btn-success">
                                <i class="fas fa-user-plus me-2"></i>
                                Nuevo Usuario
                            </button>
                        </div>

                        <div class="mb-3">
                            <input type="text" id="buscarUsuario" class="form-control" placeholder="Buscar usuarios...">
                        </div>

                        <div class="table-responsive">
                            <table id="tablaUsuarios" class="table table-striped table-hover">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th>Rut</th>
                                        <th>Nombre</th>
                                        <th>Apellido Paterno</th>
                                        <th>Apellido Materno</th>
                                        <th>Teléfono</th>
                                        <th>Rol</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php

                                    $sql = "SELECT * FROM usuarios";
                                    $result = $conn->query($sql);

                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {

                                            echo "<tr>";
                                            echo "<td>" . htmlspecialchars($row['rut']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['apellido1']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['apellido2']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['numero']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['rol']) . "</td>";
                                            echo "<td>";

                                            // Formulario para editar usuario
                                            echo "<form action='usuarios_update.php' method='POST' style='display:inline;'>";
                                            echo "<input type='hidden' name='rut' value='" . htmlspecialchars($row['rut']) . "'>";
                                            echo "<input type='hidden' name='nombre' value='" . htmlspecialchars($row['nombre']) . "'>";
                                            echo "<input type='hidden' name='apellido1' value='" . htmlspecialchars($row['apellido1']) . "'>";
                                            echo "<input type='hidden' name='apellido2' value='" . htmlspecialchars($row['apellido2']) . "'>";
                                            echo "<input type='hidden' name='telefono' value='" . htmlspecialchars($row['numero']) . "'>";
                                            echo "<input type='hidden' name='rol' value='" . htmlspecialchars($row['rol']) . "'>";
                                            echo "<button type='submit' class='btn btn-warning'>Editar</button>";
                                            echo "</form>";

                                            // Formulario para habilitar o deshabilitar usuario
                                            if ($row['estado'] == 1) {
                                                // Botón para deshabilitar
                                                echo "<form action='../data/usuarios/usuarios_disable.php' method='POST' style='display:inline; margin-left: 5px;'>";
                                                echo "<input type='hidden' name='rut' value='" . htmlspecialchars($row['rut']) . "'>";
                                                echo "<button type='submit' class='btn btn-danger'>Deshabilitar</button>";
                                                echo "</form>";
                                            } else {
                                                // Botón para habilitar
                                                echo "<form action='../data/usuarios/usuarios_enable.php' method='POST' style='display:inline; margin-left: 5px;'>";
                                                echo "<input type='hidden' name='rut' value='" . htmlspecialchars($row['rut']) . "'>";
                                                echo "<button type='submit' class='btn btn-success'>Habilitar</button>";
                                                echo "</form>";
                                            }
                                        }
                                    } else {
                                        echo "<tr><td colspan='7'>No hay usuarios registrados</td></tr>";
                                    }

                                    ?>
                                    <!-- Los usuarios se cargarán aquí dinámicamente -->
                                </tbody>
                            </table>
                        </div>

                        <div class="text-center mt-4">
                            <span id="totalUsuarios" class="badge bg-secondary p-2">
                                <i class="fas fa-car me-2"></i>
                                Total de Usuarios: <?php echo $totalUsuarios; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Crear/Editar Usuario -->
    <div class="modal fade" id="modalUsuario" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Nuevo Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formUsuario">
                        <input type="hidden" id="userRut">
                        <div class="mb-3">
                            <label for="contraseña" class="form-label">Contraseña</label>
                            <input type="text" class="form-control" id="contraseña" required>
                        </div>
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="apellido1" class="form-label">Apellido Paterno</label>
                            <input type="apellido1" class="form-control" id="apellido1" required>
                        </div>
                        <div class="mb-3">
                            <label for="apellido2" class="form-label">Apellido Materno</label>
                            <input type="apellido2" class="form-control" id="apellido2" required>
                        </div>
                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" id="telefono" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnGuardarUsuario">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>