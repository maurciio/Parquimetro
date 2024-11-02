document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('infoModal');
    const verButtons = document.querySelectorAll('.btn-wrapper.outline');

    verButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            

            // Hacemos una petición AJAX para obtener los datos del usuario
            fetch('get_user_info.php')
                .then(response => response.json())
                .then(data => {
                    const modalBody = modal.querySelector('.modal-body');
                    modalBody.innerHTML = `
                        <p><strong>Estado:</strong> ${data.estado}</p>
                        <p><strong>Nombre:</strong> ${data.nombre}</p>
                        <p><strong>Apellido 1:</strong> ${data.apellido1}</p>
                        <p><strong>Apellido 2:</strong> ${data.apellido2}</p>
                        <p><strong>RUT:</strong> ${data.rut}</p>
                    `;
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });
    });
});

