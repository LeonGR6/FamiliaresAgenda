document.addEventListener('DOMContentLoaded', function() {

    const nombreInput = document.getElementById('nombre');
    nombreInput.addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });
    const form = document.getElementById('registroForm');
    
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
            if (!response.ok) throw new Error("Error en la red");
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
                let seconds = 5;
                const countdownElement = document.getElementById('countdown');
                const timer = setInterval(() => {
                    seconds--;
                    countdownElement.textContent = seconds;
                    if (seconds <= 0) {
                        clearInterval(timer);
                        window.location.href = '/mvc-php/app/views/login.php';
                    }
                }, 1000);
            }
        })
        .catch(error => {
            alert("Error al conectar con el servidor");
            console.error("Error:", error);
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        });
    });
});