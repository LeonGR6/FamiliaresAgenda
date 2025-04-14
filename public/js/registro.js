$(document).ready(function() {
    $('#registroForm').on('submit', function(e) {
        e.preventDefault();
        const $form = $(this);
        const $submitBtn = $form.find('button[type="submit"]');
        const $errorContainer = $('#error-messages'); // Asume que tienes un div para errores

        // Función segura para obtener valores
        const getValue = (name) => {
            return $(`[name="${name}"]`).val()?.trim() || '';
        };

        const formData = {
            nombre: getValue('nombre'),
            username: getValue('username').toLowerCase(),
            password: getValue('password'),
            email: getValue('email'),
            cargo: getValue('cargo') // Agregado el campo cargo
        };

        // Validación mejorada
        const errores = [];
        
        // Validar nombre
        if (!formData.nombre) errores.push('Nombre completo es requerido');
        else if (formData.nombre.length < 3) errores.push('Nombre muy corto (mínimo 3 caracteres)');
        
        // Validar username
        if (!formData.username) errores.push('Nombre de usuario es requerido');
        else if (!/^[a-z]{4,20}$/.test(formData.username)) {
            errores.push('Usuario debe tener 4-20 letras minúsculas');
        }
        
        // Validar password
        if (!formData.password) errores.push('Contraseña es requerida');
        else if (!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/.test(formData.password)) {
            errores.push('La contraseña debe tener 8+ caracteres, mayúscula, minúscula, número y símbolo');
        }
        
        // Validar email
        if (!formData.email) errores.push('Email es requerido');
        else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.email)) {
            errores.push('Email inválido');
        }
        
        // Validar cargo
        if (!formData.cargo) errores.push('Debe seleccionar un cargo');
        else if (!['Espectador', 'Personal', 'Administrador'].includes(formData.cargo)) {
            errores.push('Cargo seleccionado no válido');
        }

        // Mostrar errores
        if (errores.length > 0) {
            $errorContainer.html(
                '<div class="alert alert-danger"><ul>' + 
                errores.map(e => `<li>${e}</li>`).join('') +
                '</ul></div>'
            );
            return;
        }

        // Envío AJAX
        $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Registrando...');
        
        $.ajax({
            type: 'POST',
            url: $form.attr('action'),
            data: $form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $form.closest('.card').hide();
                    $('#mensajeExito').show();
                    
                    let seconds = 5; // Reducido a 5 segundos
                    $('#countdown').text(seconds);
                    
                    const timer = setInterval(() => {
                        seconds--;
                        $('#countdown').text(seconds);
                        if (seconds <= 0) {
                            clearInterval(timer);
                            window.location.href = '/mvc-php/app/views/login.php';
                        }
                    }, 1000);
                } else {
                    $errorContainer.html(
                        `<div class="alert alert-danger">${response.message || 'Error en el registro'}</div>`
                    );
                }
            },
            error: function(xhr) {
                let errorMsg = 'Error en la conexión';
                try {
                    const response = JSON.parse(xhr.responseText);
                    errorMsg = response.message || errorMsg;
                } catch (e) {
                    console.error('Error parsing response', e);
                }
                $errorContainer.html(`<div class="alert alert-danger">${errorMsg}</div>`);
            },
            complete: function() {
                $submitBtn.prop('disabled', false).html('Registrarse');
                $('html, body').animate({ scrollTop: 0 }, 'slow');
            }
        });
    });
});