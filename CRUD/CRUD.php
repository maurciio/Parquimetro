<?php
// Asegúrate de que el usuario está autenticado y tiene un rut en la sesión
session_start();
require '../php/views/conexion.php';
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: /ParquimetroPHP/php/views/index.php ');
    exit;
}?>
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

<?php include "../CRUD/navUser.php"; ?>
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
                                    <!-- Los usuarios se cargarán aquí dinámicamente -->
                                </tbody>
                            </table>
                        </div>

                        <div class="text-center mt-4">
                            <span id="totalUsuarios" class="badge bg-secondary p-2">
                                <i class="fas fa-car me-2"></i>
                                Total de Usuarios: 0
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
    <script src="Usuarios.js"></script>
</body>
</html>