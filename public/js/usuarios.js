
   $(document).on('click', '.editar-usuario', function() {
    const boton = $(this);
    const id = boton.data('id');
    let nombre = boton.data('nombre');
    let username = boton.data('username');
    let email = boton.data('email');
    let cargo = boton.data('cargo');
    
    // Función para validar email
    function validarEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    // Editar Nombre
    nombre = prompt("Editar Nombre:", nombre);
    if (nombre === null) return;
    
    // Editar Username
    username = prompt("Editar Nombre de Usuario:", username);
    if (username === null) return;
    if (username.length < 3) {
        alert("El nombre de usuario debe tener al menos 3 caracteres");
        return;
    }
    
    // Editar Email
    email = prompt("Editar Email:", email);
    if (email === null) return;
    if (!validarEmail(email)) {
        alert("Por favor ingrese un email válido");
        return;
    }
    
    // Editar Cargo
    cargo = prompt("Editar Cargo (Administrador/Personal/Espectador):", cargo);
    if (cargo === null) return;
    if (!['Administrador', 'Personal', 'Espectador'].includes(cargo)) {
        alert("Cargo no válido. Debe ser: Administrador, Personal o Espectador");
        return;
    }
    
    // Confirmar cambios
    if (confirm(`¿Confirmas actualizar el usuario con estos datos?\n\nNombre: ${nombre}\nUsuario: ${username}\nEmail: ${email}\nCargo: ${cargo}`)) {
        // Feedback visual
        boton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
        
        $.ajax({
            url: '../controllers/update_usuario.php',
            type: 'POST',
            data: {
                id: id,
                nombre: nombre,
                username: username,
                email: email,
                cargo: cargo
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert("✅ " + response.success);
                    location.reload(); // Recargar para ver cambios
                } else {
                    alert("⚠️ " + (response.error || "Error al actualizar"));
                }
            },
            error: function(xhr) {
                alert("❌ Error de conexión");
                console.error("Error detallado:", xhr.responseText);
            },
            complete: function() {
                boton.prop('disabled', false).html('<i class="fas fa-edit"></i> Editar');
            }
        });
    }
});



    





    $(document).on('click', '.eliminar-usuario', function() {
    const boton = $(this);
    const id = boton.data('id');
    const nombreUsuario = boton.closest('tr').find('td:nth-child(2)').text();
    
    if (!confirm(`¿Estás seguro de eliminar al usuario ${nombreUsuario}?`)) {
        return;
    }

    // Feedback visual
    const textoOriginal = boton.html();
    boton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Eliminando...');

    $.ajax({
        url: '../controllers/delete_usuario.php',
        type: 'POST',
        data: { id: id },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Eliminar la fila con animación
                boton.closest('tr').fadeOut(400, function() {
                    $(this).remove();
                });
                // Mostrar notificación de éxito
                alert('Usuario eliminado correctamente');
            } else {
                alert('Error: ' + response.message);
                boton.prop('disabled', false).html(textoOriginal);
            }
        },
        error: function(xhr) {
            let errorMsg = 'Error al conectar con el servidor';
            try {
                const response = JSON.parse(xhr.responseText);
                errorMsg = response.message || errorMsg;
            } catch (e) {
                console.error('Error en la respuesta:', xhr.responseText);
            }
            alert(errorMsg);
            boton.prop('disabled', false).html(textoOriginal);
        }
    });
});

