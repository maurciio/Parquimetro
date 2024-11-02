$(document).ready(function() {
    cargarUsuarios();

    $('#usuarioForm').submit(function(e) {
        e.preventDefault();
        guardarUsuario();
    });
});

function cargarUsuarios() {
    $.ajax({
        url: 'Usuarios.php',
        type: 'POST',
        data: { accion: 'obtener' },
        dataType: 'json',
        success: function(usuarios) {
            var tabla = $('#usuariosTabla');
            tabla.empty();
            usuarios.forEach(function(usuario) {
                tabla.append(`
                    <tr>
                        <td>${usuario.rut}</td>
                        <td>${usuario.nombre}</td>
                        <td>${usuario.apellido1}</td>
                        <td>${usuario.apellido2}</td>
                        <td>${usuario.telefono}</td>
                        <td>${usuario.rol}</td>
                
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="editarUsuario(${usuario.rut}, '${usuario.nombre}', '${usuario.apellido1}','${usuario.apellido2}','${usuario.telefono}', '${usuario.rut}')">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="eliminarUsuario(${usuario.rut})">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </td>
                    </tr>
                `);
            });
        }
    });
}

function guardarUsuario() {
    var rut = $('#userRut').val();
    var nombre = $('#nombre').val();
    var apellido1 = $('#apellido1').val();
    var apellido2 = $('#apellido2').val();
    var telefono = $('#telefono').val();
    var rol = $('#rol').val();
    var accion = rut ? 'actualizar' : 'crear';

    $.ajax({
        url: 'usuarios.php',
        type: 'POST',
        data: {
            accion: accion,
            id: id,
            nombre: nombre,
            email: email,
            telefono: telefono
        },
        dataType: 'json',
        success: function(response) {
            if (response.exito) {
                alert(response.mensaje);
                limpiarFormulario();
                cargarUsuarios();
            } else {
                alert('Error: ' + response.error);
            }
        }
    });
}

function editarUsuario(id, nombre, apellido1,apellido2,telefono,rol) {
    $('#userRut').val(Rut);
    $('#nombre').val(nombre);
    $('#apellido1').val(apellido1);
    $('#apellido2').val(apellido2);
    $('#telefono').val(telefono);
    $('#rol').val(rol);
}

function eliminarUsuario(id) {
    if (confirm('¿Estás seguro de que quieres eliminar este usuario?')) {
        $.ajax({
            url: 'usuarios.php',
            type: 'POST',
            data: {
                accion: 'eliminar',
                id: id
            },
            dataType: 'json',
            success: function(response) {
                if (response.exito) {
                    alert(response.mensaje);
                    cargarUsuarios();
                } else {
                    alert('Error: ' + response.error);
                }
            }
        });
    }
}

function limpiarFormulario() {
    $('#userRut').val(Rut);
    $('#nombre').val(nombre);
    $('#apellido1').val(apellido1);
    $('#apellido2').val(apellido2);
    $('#telefono').val(telefono);
    $('#rol').val(rol);
}