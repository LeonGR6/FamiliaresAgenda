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
});