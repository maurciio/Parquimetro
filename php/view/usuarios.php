<?php
// Asegúrate de que el usuario está autenticado y tiene un rut en la sesión
session_start();

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: /ParquimetroPHP/php/views/index.php ');
    exit;
}

if (!isset($_SESSION['rut'])) {
    echo "<script>window.location.href = 'index.php';</script>";
    exit;
}

require '../conexion.php';
require '../component/navegacion.php';

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
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <link rel="stylesheet" href="../view/css/styles.css">
    <style>

    </style>
</head>

<body>
    <!-- Barra de navegación -->
    <?php renderNav() ?>

    <!-- Contenedor principal -->
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <h1 class="h4 mb-0">
                    <i class="fas fa-parking me-2"></i>
                    Gestión de Usuarios de Parquímetros
                </h1>
                <button id="btnNuevoUsuario" class="btn btn-success">
                    <i class="fas fa-user-plus me-2"></i>
                    Nuevo Usuario
                </button>
            </div>

            <div class="card-body">
                <!-- Campo de búsqueda -->
                <div class="mb-4">
                    <input type="text" id="buscarUsuario" class="form-control" placeholder="Buscar usuarios...">
                </div>

                <!-- Tabla de usuarios -->
                <div class="table-responsive">
                    <table id="tablaUsuarios" class="table table-striped table-hover">
                        <thead>
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

                                    // Botón para editar usuario, que abre el modal con AJAX
                                    echo "<button type='button' class='btn btn-warning btn-sm btnEditar' data-rut='" . htmlspecialchars($row['rut']) . "'>Editar</button>";

                                    // Botón para habilitar o deshabilitar usuario según el estado
                                    if ($row['estado'] == 1) {
                                        echo "<form action='../data/usuarios/usuarios_disable.php' method='POST' style='display:inline; margin-left: 5px;'>";
                                        echo "<input type='hidden' name='rut' value='" . htmlspecialchars($row['rut']) . "'>";
                                        echo "<button type='submit' class='btn btn-danger btn-sm'>Deshabilitar</button>";
                                        echo "</form>";
                                    } else {
                                        echo "<form action='../data/usuarios/usuarios_enable.php' method='POST' style='display:inline; margin-left: 5px;'>";
                                        echo "<input type='hidden' name='rut' value='" . htmlspecialchars($row['rut']) . "'>";
                                        echo "<button type='submit' class='btn btn-success btn-sm'>Habilitar</button>";
                                        echo "</form>";
                                    }

                                    echo "</td></tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7' class='text-center'>No hay usuarios registrados</td></tr>";
                            }
                            ?>
                        </tbody>

                    </table>
                </div>

                <!-- Total de usuarios -->
                <div class="text-center mt-4">
                    <span id="totalUsuarios" class="badge bg-secondary p-2">
                        <i class="fas fa-car me-2"></i>
                        Total de Usuarios: <?php echo $totalUsuarios; ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalUsuario" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Nuevo Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formUsuario">
                        <div class="mb-3">
                            <label for="rutUsuario" class="form-label">Rut</label>
                            <input type="text" class="form-control" id="rutUsuario" name="rut" placeholder="Ej: 12345678-9" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nombreUsuario" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombreUsuario" name="nombre" placeholder="Nombre" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="telefonoUsuario" class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="telefonoUsuario" name="telefono" placeholder="Ej: +56912345678" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="apellidoPaterno" class="form-label">Apellido Paterno</label>
                                <input type="text" class="form-control" id="apellidoPaterno" name="apellido1" placeholder="Apellido Paterno" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="apellidoMaterno" class="form-label">Apellido Materno</label>
                                <input type="text" class="form-control" id="apellidoMaterno" name="apellido2" placeholder="Apellido Materno" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="contrasenaUsuario" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="contrasenaUsuario" name="contraseña" placeholder="Contraseña" required>
                        </div>

                        <div class="mb-3">
                            <label for="rolUsuario" class="form-label">Rol</label>
                            <select class="form-select" id="rolUsuario" name="rol" required>
                                <option value="" disabled selected>Selecciona un rol</option>
                                <option value="operador">Operador</option>
                                <option value="administrador">Administrador</option>
                            </select>
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
    <script src=""></script>
    <script>
        $(document).ready(function() {
            // Mostrar modal para crear un nuevo usuario
            $('#btnNuevoUsuario').click(function() {
                $('#formUsuario')[0].reset();
                $('#modalTitle').text('Nuevo Usuario');
                $('#rutUsuario').prop('readonly', false); // Habilitar campo rut para nuevo usuario
                $('#btnGuardarUsuario').data('action', 'create');
                $('#modalUsuario').modal('show');
            });

            // Enviar solicitud de creación o edición
            $('#btnGuardarUsuario').click(function() {
                const action = $(this).data('action');
                const url = action === 'create' ? '../data/usuarios/usuarios_create.php' : '../data/usuarios/usuarios_update.php';
                const formData = {
                    rut: $('#rutUsuario').val(),
                    contraseña: $('#contrasenaUsuario').val(),
                    nombre: $('#nombreUsuario').val(),
                    apellido1: $('#apellidoPaterno').val(),
                    apellido2: $('#apellidoMaterno').val(),
                    telefono: $('#telefonoUsuario').val(),
                    rol: $('#rolUsuario').val(),
                };

                $.post(url, formData, function(response) {
                    alert(response);
                    $('#modalUsuario').modal('hide');
                    location.reload();
                });
            });
        });

        $(document).ready(function() {
                // Cargar datos del usuario en el formulario de edición
                $('#tablaUsuarios').on('click', '.btnEditar', function() {
                    const rut = $(this).data('rut');
                    $.get('../data/usuarios/usuarios_read.php', {
                        rut: rut
                    }, function(usuario) {
                        $('#rutUsuario').val(usuario.rut).prop('readonly', true); // Deshabilitar edición de rut en modo edición
                        $('#contrasenaUsuario').val(''); // La contraseña se establece vacía para permitir cambiarla si se desea
                        $('#nombreUsuario').val(usuario.nombre);
                        $('#apellidoPaterno').val(usuario.apellido1);
                        $('#apellidoMaterno').val(usuario.apellido2);
                        $('#telefonoUsuario').val(usuario.numero);
                        $('#rolUsuario').val(usuario.rol);
                        $('#modalTitle').text('Editar Usuario');
                        $('#btnGuardarUsuario').data('action', 'edit');
                        $('#modalUsuario').modal('show');
                    }, 'json');
                });
            });
    </script>


</body>

</html>