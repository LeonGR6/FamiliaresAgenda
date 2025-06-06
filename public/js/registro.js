document.addEventListener('DOMContentLoaded', function() {

    const nombreInput = document.getElementById('nombre');
    nombreInput.addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });
    const form = document.getElementById('registroForm');

     // Validación básica del cliente
         const formData = {
             nombre: nombreInput.value.trim(),
             username: document.getElementById('username').value.trim(),
             password: document.getElementById('password').value,
             email: document.getElementById('email').value.trim(),
             cargo: document.getElementById('cargo').value
         };
         
         const errors = [];
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Mostrar carga en el botón
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Procesando...';
        
        // Enviar datos al servidor
        fetch(form.action, {
            method: 'POST',
            body: new FormData(form)
        })
        .then(response => {
            if (!response.ok) throw new Error(" ❌ Error en la red");
            return response.json();
        })
        .then(data => {
            // Mostrar alerta con la respuesta del servidor
            alert(data.message);
            
            if (data.success) {
                // Ocultar formulario y mostrar mensaje de éxito
                document.getElementById('registroCard').style.display = 'none';
                document.getElementById('mensajeExito').style.display = 'block';
                
                // Redirección automática
                let seconds = 10;
                const countdownElement = document.getElementById('countdown');
                const timer = setInterval(() => {
                    seconds--;
                    countdownElement.textContent = seconds;
                    if (seconds <= 0) {
                        clearInterval(timer);
                        window.location.href = '/SIADO-PJAGS/app/views/login.php';
                    }
                }, 1000);
            }
        })
        .catch(error => {
            alert("❌ Error al conectar con el servidor");
            console.error("Error:", error);
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        });
    });
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