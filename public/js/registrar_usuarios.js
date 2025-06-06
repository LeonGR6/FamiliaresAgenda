
document.addEventListener('DOMContentLoaded', function() {
    const nombreInput = document.getElementById('nombre');
    const form = document.getElementById('registroForm');

    // Convertir nombre a mayúsculas al escribir
    if (nombreInput) {
        nombreInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    }

    // Manejar el envío del formulario
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Mostrar carga en el botón
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Procesando...';
            
            // Validación básica del cliente (opcional)
            const password = document.getElementById('password').value;
            if (password.length < 8) {
                alert('❌ La contraseña debe tener al menos 8 caracteres');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
                return;
            }
            
            // Enviar datos al servidor
            fetch(form.action, {
                method: 'POST',
                body: new FormData(form)
            })
            .then(response => {
                if (!response.ok) throw new Error("❌ Error en la respuesta del servidor");
                return response.json();
            })
            .then(data => {
                // Mostrar alerta con el mensaje del servidor
                alert(data.message);
                
                // Si fue exitoso, redirigir después de cerrar el alert
                if (data.success) {
                    window.location.href = '/SIADO-PJAGS/app/views/usuarios.php';
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("❌ Error al conectar con el servidor: " + error.message);
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            });
        });
    }
});

document.addEventListener('DOMContentLoaded', function () {
    const nombreInput = document.getElementById('nombre');
    const usernameInput = document.getElementById('username');

    nombreInput.addEventListener('input', function () {
        const nombreCompleto = nombreInput.value.trim().split(' ');
        if (nombreCompleto.length >= 2) {
            const primerNombre = nombreCompleto[0];
            const primerApellido = nombreCompleto[1];
            const username = primerNombre.substring(0, 2).toLowerCase() + primerApellido.substring(0, 4).toLowerCase();
            usernameInput.value = username;
        } else {
            usernameInput.value = '';
        }
    });
});