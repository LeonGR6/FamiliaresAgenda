document.getElementById('registroForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Deshabilitar el botón para evitar múltiples envíos
    const submitButton = this.querySelector('button[type="submit"]');
    submitButton.disabled = true;
    const originalText = submitButton.textContent;
    submitButton.textContent = 'Validando...';

    const formData = new FormData(this);
    
    fetch(this.action, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) throw new Error('Error en la petición');
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert(' ✅ Login exitoso.');
            window.location.href = data.redirect;
        } else {
            alert('❌ Error: ' + data.message);
            // Enfocar campo correspondiente
            if (data.message.includes('Usuario')) {
                document.getElementById('username').focus();
            } else if (data.message.includes('contraseña')) {
                document.getElementById('password').focus();
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert(' ❌ Error de conexión con el servidor');
    })
    .finally(() => {
        // Restaurar botón
        submitButton.disabled = false;
        submitButton.textContent = originalText;
    });
});