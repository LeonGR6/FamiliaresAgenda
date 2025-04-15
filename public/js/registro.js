<<<<<<< HEAD
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
=======
document.addEventListener('DOMContentLoaded', function() {
    // Elementos del DOM
    const registroForm = document.getElementById('registroForm');
    const registroCard = document.getElementById('registroCard');
    const mensajeExito = document.getElementById('mensajeExito');
    const countdownElement = document.getElementById('countdown');
    
    // Verificar que todos los elementos existan
    if (!registroForm || !registroCard || !mensajeExito || !countdownElement) {
        console.error('Error: Elementos esenciales del formulario no encontrados');
        return;
    }
    
    // Convertir nombre a mayúsculas al escribir
    const nombreInput = document.getElementById('nombre');
    if (nombreInput) {
        nombreInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    }
    
    // Manejar envío del formulario
    registroForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Deshabilitar botón para evitar múltiples envíos
        const submitBtn = this.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Registrando...';
        }
        
        // Validación básica del cliente
        const formData = {
            nombre: nombreInput.value.trim(),
            username: document.getElementById('username').value.trim(),
            password: document.getElementById('password').value,
            email: document.getElementById('email').value.trim(),
            cargo: document.getElementById('cargo').value
        };
        
        const errors = [];
        
        if (!formData.nombre) errors.push('El nombre completo es requerido');
        if (!formData.username) errors.push('El nombre de usuario es requerido');
        if (!formData.password) errors.push('La contraseña es requerida');
        if (!formData.email) errors.push('El email es requerido');
        if (!formData.cargo) errors.push('Debe seleccionar un cargo');
        
        if (errors.length > 0) {
            showAlertErrors(errors);
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Registrar';
            }
            return;
        }
        
        // Enviar formulario
        fetch(registroForm.action, {
            method: 'POST',
            body: new FormData(registroForm),
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess();
            } else {
                throw new Error(data.message || 'Error en el registro');
            }
        })
        .catch(error => {
            showAlertError(error.message);
        })
        .finally(() => {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Registrar';
            }
        });
    });
    
    // Función para mostrar múltiples errores como alertas
    function showAlertErrors(errors) {
        // Crear mensaje con todos los errores
        let errorMessage = "Por favor corrige los siguientes errores:\n\n";
        errorMessage += errors.join('\n\n');
        
        // Mostrar alerta nativa
        alert(errorMessage);
    }
    
    // Función para mostrar un solo error como alerta
    function showAlertError(message) {
        alert("Error: " + message);
    }
    
    function showSuccess() {
        registroCard.style.display = 'none';
        mensajeExito.style.display = 'block';
        
        let seconds = 5;
        countdownElement.textContent = seconds;
        
        const timer = setInterval(() => {
            seconds--;
            countdownElement.textContent = seconds;
            
            if (seconds <= 0) {
                clearInterval(timer);
                window.location.href = '/mvc-php/app/views/login.php';
            }
        }, 1000);
    }
>>>>>>> ff3078a (Primer commit: Inicialización del proyecto)
});