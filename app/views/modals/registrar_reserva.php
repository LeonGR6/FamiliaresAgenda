<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../views/login.php");
    exit();
}

require_once '../inc/header.php'; 
require_once '../inc/navbar_default.php';
?>

<body>
    <div class="container-fluid">
        <div class="row">
        
            <div class="container-fluid col-md-12 col-lg-10 mx-auto mt-4 mb-5">
                <div class="card-header text-center text-dark">
                    <h1 class="h3 mb-0" style="font-size: calc(1.2rem + 0.6vw)">Registro de Reservaciones</h1>
                </div>
                <br>
                
                
                    
                <form id="formReserva" method="post" class="card shadow mx-auto p-4 text-dark"  style="max-width: 700px; margin: 0 auto;">
                    
                    <!-- Mensaje de respuesta -->
                    <div id="responseMessage" class="alert d-none"></div>

                    <div class="form-group mb-3">
                        <label for="numero_carpeta">Número y Año de Carpeta</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">EXPEDIENTE-</span>
                            </div>
                            <input type="text" class="form-control" id="numero_carpeta" name="numero_carpeta" 
                                placeholder="1245-2024" pattern="\d+-\d{4}" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <small class="form-text text-muted">Formato correcto: 1245-2024</small>
                    </div>

                    <div class="row mb-3">
                    <!-- Primera columna - Campo de fecha -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="fecha">Fecha</label>
                            <input type="date" class="form-control" id="fecha" name="fecha" required>
                            <div class="invalid-feedback">Por favor ingrese una fecha válida</div>
                        </div>
                    </div>

                    <!-- Segunda columna - Campo de hora -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="hora">Hora</label>
                            <input type="time" class="form-control" id="hora" name="hora" step="60" pattern="[0-9]{2}:[0-9]{2}" required>
                            <div class="invalid-feedback">Ingrese una hora válida</div>
                        </div>
                    </div>

                    <!-- Tercera columna - Campo de duración -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="duracion">Duración (minutos)</label>
                            <input type="number" class="form-control" id="duracion" name="duracion" 
                                placeholder="Ej: 30" min="30" step="1" required>
                            <div class="invalid-feedback">La duración mínima es 30 minutos</div>
                        </div>
                    </div>
                </div>

                    <div class="form-group mb-3">
                        <label for="tipo">Tipo</label>
                        <select class="form-control" id="tipo" name="tipo" required>
                        <option value="" >Seleccione Tipo Audiencia</option>
                            <option value="AUDIENCIAS CONCILIACIÓN">CONCILIACIÓN</option>
                            <option value="AUDIENCIAS DESAHOGO DE PRUEBAS">DESAHOGO DE PRUEBAS</option>
                            <option value="AUDIENCIAS ESCUCHA DE MENORES">ESCUCHA DE MENORES</option>
                            <option value="AUDIENCIAS ALEGATOS">ALEGATOS</option>
                            </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="puesto">Puesto</label>
                        <input type="text" class="form-control text-uppercase" id="puesto" name="puesto" 
                            placeholder="ANALISTA JR 1" 
                            pattern="[A-Z0-9 ]+" 
                            oninput="this.value = this.value.toUpperCase(); this.value = this.value.replace(/[^A-Z0-9 ]/g, '');"
                            required>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="observaciones">Observaciones</label>
                        <textarea class="form-control text-uppercase" id="observaciones" name="observaciones" 
                                rows="2" maxlength="500"
                                oninput="this.value = this.value.toUpperCase();"
                                placeholder="INFORMACIÓN ADICIONAL (OPCIONAL)"></textarea>
                        <small class="form-text text-muted">Máximo 500 caracteres</small>
                    </div>

                    <div class="form-group mb-4">
                        <label for="motivo">Motivo</label>
                        <input type="text" class="form-control text-uppercase" id="motivo" name="motivo"
                                placeholder="SOLICITUD DE REVISIÓN"
                                oninput="this.value = this.value.toUpperCase()">
                    </div>

                    <div class="form-group mb-3">
                        <label for="juzgado">Juzgado</label>
                        <select class="form-control" id="juzgado" name="juzgado" required>
                        <option value="">Seleccione un juzgado</option>
                            <option value="1 F">1 FAMILIARES</option>
                            <option value="2 F">2 FAMILIARES</option>
                            <option value="3 F">3 FAMILIARES</option>
                            </select>
                    </div>

                    <input type="hidden" name="estado" value="Pendiente">
                    <input type="hidden" name="oculto" value="1">
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                        <button type="button" class="btn btn-danger px-4" onclick="window.location.href='../registro-app.php'">Cancelar</button>
                            <button type="reset" class="btn btn-warning px-4 me-2">Limpiar</button>
                        </div>
                        <button type="submit" id="btnSubmit" class="btn btn-primary px-4">
                            <span id="submitText">Enviar</span>
                            <span id="submitSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    
document.getElementById('formReserva').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Mostrar estado de carga
    const submitBtn = document.getElementById('btnSubmit');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = 'Procesando...';
    
    // Enviar datos al controlador
    fetch('../../controllers/insert.php', {
        method: 'POST',
        body: new FormData(this)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la red');
        }
        return response.json();
    })
    .then(data => {
        // Mostrar alerta nativa
        alert(data.message);
        
        // Si fue éxito, limpiar formulario
        if (data.success) {
            document.getElementById('formReserva').reset();
            
            // Opcional: Redireccionar después de 1 segundo
            setTimeout(() => {
                window.location.href = '../registro-app.php';
            }, 1000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Error al conectar con el servidor');
    })
    .finally(() => {
        // Restaurar botón
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});


    // Limpiar errores al editar
    document.querySelectorAll('.form-control').forEach(input => {
        input.addEventListener('input', function() {
            this.classList.remove('is-invalid');
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
    const HoraInput = document.getElementById('hora');
    
    // Configurar el paso a 60 segundos (1 minuto) para eliminar los segundos
    HoraInput.step = 60;
    
    // Opcional: Formatear el valor al perder el foco
   
});
// Solución para Chrome/Edge (elimina segundos al seleccionar)
document.getElementById('hora').addEventListener('change', function() {
        if(this.value.length > 5) {
            this.value = this.value.substring(0, 5); // Corta los segundos
        }
    });




    </script>


