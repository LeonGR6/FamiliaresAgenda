<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../views/login.php");
    exit();
}

require_once '../inc/header.php';
require_once '../inc/navbar_default.php';

// Incluir archivo de configuración de la base de datos
include("../../models/conexion.php");

try {
    // Consulta SQL modificada para incluir duración
    $sql = "SELECT 
                ID AS id, 
                NumeroCarpeta,
                TipoProcedimiento,
                Fecha, 
                Hora, 
                Duracion,
                cargo,
                Juzgado,
                Sala
            FROM Reservas
            "; // Filtro opcional
    
    $stmt = $db->prepare($sql);
    $stmt->execute();

    // Procesar las citas para el formato de FullCalendar
    $citas = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $start = new DateTime($row['Fecha'] . ' ' . $row['Hora']);
        $duracion = $row['Duracion'] ?? 30; // Duración predeterminada de 30 minutos si es NULL
        
        $citas[] = [
            'id' => $row['id'],
            'numeroCarpeta' => $row['NumeroCarpeta'], // Asegúrate de incluir este campo
            'juzgado' => $row['Juzgado'],
            'start' => $start->format('Y-m-d\TH:i:s'),
            'end' => $start->modify("+{$duracion} minutes")->format('Y-m-d\TH:i:s'),
            'extendedProps' => [
                'cargo' => $row['cargo'],
                'duracion' => $duracion,
                'tipoProcedimiento' => $row['TipoProcedimiento'],
                'sala' => $row['Sala']
            ],
            'className' => 'duracion-' . $duracion . ' juzgado-' . preg_replace('/\D/', '', $row['Juzgado']) // Clases CSS para duración y juzgado
        ];
    }
    
    $citas_json = json_encode($citas, JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    error_log("Error en la base de datos: " . $e->getMessage());
    $citas_json = '[]';
}

$db = null;
?>


<body>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10 mt-4 mb-5">
                <div class="card-header text-center text-dark">
                    <h1 class="h3 mb-0" style="font-size: calc(1.2rem + 0.6vw)">Registro de Audiencias</h1>
                    <p class="text-muted" style="font-size: 0.9rem;">Realiza el registro de audiencias presenciales</p>
                </div>
                <br>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card shadow p-4 text-dark h-100 d-flex flex-column">
                            <h2 class="h4 text-dark text-center mb-3">Calendario de Audiencias</h2>
                            <div class="form-group mb-3">
                            <p class="text-secondary mb-2 text-center mx-auto"><b>Filtrar por sala:</b></p>
                                <select class="form-select w-75 mx-auto form-control" id="salaFilter">
                                    <option value="todas">Todas las Salas</option>
                                    <option value="SALA 1">SALA 1</option>
                                    <option value="SALA 2">SALA 2</option>
                                    <option value="SALA 3">SALA 3</option>
                                    <option value="CAMARA 1">CAMARA GESELL 1</option>
                                    <option value="CAMARA 2">CAMARA GESELL 2</option>
                                </select>
                            </div>
                            <div id='calendar' class='text-dark flex-grow-1'>
                            </div>
                            <div class="row text-center">
                                <div class="col color-1 p-2 text-dark fw-bold"><div class="rounded p-2 fw-bold" style="background-color: #afda8c;"></div>1° Familiar</div> 
                                <div class="col color-2 p-2 text-dark fw-bold"><div class="rounded p-2 fw-bold" style="background-color: #8cd6da;"></div>2° Familiar</div>
                                <div class="col color-3 p-2 text-dark fw-bold"><div class="rounded p-2 fw-bold" style="background-color: #b78cda"></div>3° Familiar</div>
                                <div class="col color-4 p-2 text-dark fw-bold"><div class="rounded p-2 fw-bold" style="background-color: #da908c"></div>4° Familiar</div>
                                <div class="col color-5 p-2 text-dark fw-bold"><div class="rounded p-2 fw-bold" style="background-color: #FFEEAD;"></div>6° Familiar</div>
                        </div>
                        </div>
                        
                    </div>
                    


                    <div class="col-md-6 mb-4">
                        <form id="formReserva" method="post" class="card shadow p-4 text-dark h-100 d-flex flex-column">
                            <div id="responseMessage" class="alert d-none"></div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="fecha">Fecha</label>
                                        <input type="date" class="form-control" id="fecha" name="fecha" required>
                                        <div class="invalid-feedback">Por favor ingrese una fecha válida</div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="hora">Hora</label>
                                        <input type="time" class="form-control" id="hora" name="hora" step="60" pattern="[0-9]{2}:[0-9]{2}" required>
                                        <div class="invalid-feedback">Ingrese una hora válida</div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="duracion">Duración aprox. (min)</label>
                                        <input type="number" class="form-control" id="duracion" name="duracion"
                                            placeholder="Ej: 30" min="30" step="1" required>
                                        <div class="invalid-feedback">La duración mínima es 30 minutos</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="numero_carpeta">EXPEDIENTE</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">EXPEDIENTE</span>
                                            </div>
                                            <input 
                                                type="text"
                                                class="form-control"
                                                id="numero_carpeta"
                                                name="numero_carpeta"
                                                placeholder="NNNN-AAAA"
                                                inputmode="numeric"
                                                pattern="\d{4}-\d{4}"
                                                maxlength="9"
                                                required
                                                oninput="this.value = this.value.replace(/\D/g, '').replace(/^(\d{4})(\d{0,4})$/, '$1-$2').substring(0, 9)"
                                                onkeydown="return event.key !== '-'">
                                            <div class="invalid-feedback">Formato incorrecto (Debe ser: NNNN-AAAA)</div>
                                        </div>
                                        <small class="form-text text-muted">Formato correcto:  (Ej: 0155-2025)</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="juzgado">Juzgado</label>
                                        <select class="form-control" id="juzgado" name="juzgado" required>
                                            <option value="">Seleccione un juzgado</option>
                                            <option value="1° F">1° FAMILIAR</option>
                                            <option value="2° F">2° FAMILIAR</option>
                                            <option value="3° F">3° FAMILIAR</option>
                                            <option value="4° F">4° FAMILIAR</option>
                                            <option value="6° F">6° FAMILIAR</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tipo">Tipo</label>
                                        <select class="form-control" id="tipo" name="tipo" required>
                                            <option value="">Seleccione Tipo Audiencia</option>
                                            <option value="AUDIENCIA DE CONCILIACIÓN">CONCILIACIÓN</option>
                                            <option value="AUDIENCIA DE DESAHOGO DE PRUEBAS">DESAHOGO DE PRUEBAS</option>
                                            <option value="AUDIENCIA DE ESCUCHA DE MENORES">ESCUCHA DE MENORES</option>
                                            <option value="AUDIENCIA DE ALEGATOS">ALEGATOS</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="sala">Sala</label>
                                        <select class="form-control" id="sala" name="sala" required>
                                            <option value="">Seleccione el espacio deseado</option>
                                            <option value="SALA">SALA</option>
                                            <option value="CAMARA">CAMARA GESEL</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="observaciones">Observaciones</label>
                                <textarea class="form-control text-uppercase" id="observaciones" name="observaciones"
                                    rows="2" maxlength="500"
                                    oninput="this.value = this.value.toUpperCase();"
                                    placeholder="INFORMACIÓN ADICIONAL (OPCIONAL)"></textarea>
                                <small class="form-text text-muted">Máximo 500 caracteres</small>
                            </div>

                            <input type="hidden" name="estado" value="Pendiente">
                            <input type="hidden" name="oculto" value="1">
                            
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <div>
                                    <button type="button" class="btn btn-danger px-4" onclick="window.location.href='../registro-app.php'">Cancelar</button>
                                    <button type="reset" class="btn btn-warning px-4 me-2">Limpiar</button>
                                </div>
                                <button type="submit" id="btnSubmit" class="btn btn-primary px-4">
                                    <span id="submitText">Guardar</span>
                                    <span id="submitSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
    #infooral-img {
        position: fixed;
        bottom: 15px;
        right: 15px;
        z-index: 9999;
        width: 160px; /* Tamaño grande */
        height: auto;
    }
</style>
<img id="infooral-img" src="/mvc-php/public/images/infooral.png" alt="Info Oral">


    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.11/locales-all.global.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Pasar los eventos del calendario a JavaScript
        window.allEvents = <?php echo $citas_json; ?>;
    </script>

    <script>
        document.getElementById('formReserva').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Mostrar estado de carga
            const submitBtn = document.getElementById('btnSubmit');
            const submitText = document.getElementById('submitText');
            const submitSpinner = document.getElementById('submitSpinner');

            submitBtn.disabled = true;
            submitText.classList.add('d-none');
            submitSpinner.classList.remove('d-none');
            
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
                // Mostrar alerta de SweetAlert2
                Swal.fire({
                    icon: data.success ? 'success' : 'error',
                    title: data.success ? 'Éxito' : 'Error',
                    text: data.message,
                    confirmButtonColor: '#fc4848',
                    willClose: () => {  // Se ejecuta justo antes de cerrar
                    location.reload();
                        }
                });
                
                // Si fue éxito, limpiar formulario
                if (data.success) {
                    
                    document.getElementById('formReserva').reset();
                    // Refrescar el calendario después de un nuevo registro
                    if (typeof calendar !== 'undefined') {
                        calendar.refetchEvents();
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: '❌ Error al conectar con el servidor',
                    confirmButtonColor: '#fc4848'
                });
            })
            .finally(() => {
                // Restaurar botón
                submitBtn.disabled = false;
                submitText.classList.remove('d-none');
                submitSpinner.classList.add('d-none');
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
            HoraInput.step = 60; // Configurar el paso a 60 segundos (1 minuto) para eliminar los segundos
        });

        // Solución para Chrome/Edge (elimina segundos al seleccionar)
        document.getElementById('hora').addEventListener('change', function() {
            if(this.value.length > 5) {
                this.value = this.value.substring(0, 5); // Corta los segundos
            }
        });

        // FullCalendar Initialization
        document.addEventListener('DOMContentLoaded', function() {
            const tooltip = document.createElement('div');
            tooltip.id = 'event-tooltip';
            tooltip.style.display = 'none';
            document.body.appendChild(tooltip);
            
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                
                initialView: 'timeGridDay',
                locale: 'es',
                slotMinTime: '08:00:00',
                slotMaxTime: '20:00:00',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listDay'
                },
            
                
                views: {
                    dayGridMonth: {
                        dayHeaderFormat: { weekday: 'short' },
                        dayMaxEventRows: 3, // Mostrar exactamente 3 eventos
                        moreLinkContent: function(args) {
                            return '+ más ' + args.num;
                        },
                    },
                    listDay: {
                        type: 'list',
                        duration: { days: 1 },
                        buttonText: 'lista',
                    }
                },
                
                slotLabelFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                },
                
                allDaySlot: false,
                navLinks: true,
                slotLabelInterval: '00:30:00',
                dayMaxEvents: true,
                noEventsContent: 'No hay eventos programados',
                events: function(info, successCallback, failureCallback) {
                    const salaSeleccionada = document.getElementById('salaFilter').value;
                    const eventosFiltrados = window.allEvents.filter(evento => {
                        return salaSeleccionada === 'todas' || evento.extendedProps.sala === salaSeleccionada;
                    });
                    successCallback(eventosFiltrados);
                },
                
                eventDisplay: 'block',
                
                eventDidMount: function(arg) {
                    if (arg.view.type !== 'listDay') {
                        arg.el.addEventListener('mouseenter', function() {
                            const tooltip = document.getElementById('event-tooltip');
                            if (!tooltip) return;
                            
                            const content = `
                                <div class="tooltip-content">
                                    <strong>Carpeta:</strong> ${arg.event.extendedProps.numeroCarpeta}<br>
                                    <strong>Tipo:</strong> ${arg.event.extendedProps.tipoProcedimiento}<br>
                                    <strong>Duración:</strong> ${arg.event.extendedProps.duracion} min<br>
                                    <strong>Juzgado:</strong> ${arg.event.extendedProps.juzgado}<br>
                                    <em>Click para ver más detalles</em>
                                </div>
                            `;
                            
                            tooltip.innerHTML = content;
                            
                            setTimeout(() => {
                                const rect = arg.el.getBoundingClientRect();
                                const tooltipWidth = tooltip.offsetWidth;
                                
                                tooltip.style.left = `${rect.left + (rect.width / 2)}px`;
                                tooltip.style.top = `${rect.top - 10}px`;
                                tooltip.style.display = 'block';
                                
                                // Ajustar si se sale de la pantalla
                                const rightEdge = rect.left + (rect.width / 2) + (tooltipWidth / 2);
                                if (rightEdge > window.innerWidth) {
                                    tooltip.style.left = `${window.innerWidth - tooltipWidth - 10}px`;
                                }
                            }, 10);
                        });
                        
                        arg.el.addEventListener('mouseleave', function() {
                            const tooltip = document.getElementById('event-tooltip');
                            if (tooltip) {
                                tooltip.style.display = 'none';
                            }
                        });
                    }
                },
                
                eventContent: function(arg) {
                    let content = document.createElement('div');
                    content.className = 'fc-event-content';
                    
                    if (arg.view.type === 'dayGridMonth') {
                        content.innerHTML = `
                            <div style="display: flex; justify-content: space-between;">
                                ${arg.timeText} ${arg.event.extendedProps.numeroCarpeta}
                            </div>
                        `;
                    } else if (arg.view.type === 'listDay') {
                        content.innerHTML = `
                            <div style="display: flex; justify-content: space-between;">
                                <div>${arg.timeText}</div>
                                <div><strong>${arg.event.extendedProps.tipoProcedimiento}</strong></div>
                                <div>${arg.event.extendedProps.numeroCarpeta}</div>
                            </div>
                        `;
                    } else {
                        content.innerHTML = `
                            <div class="fc-event-time">${arg.timeText}</div>
                            <div class="fc-event-carpeta">${arg.event.extendedProps.numeroCarpeta}</div>
                        `;
                    }
                    
                    return { domNodes: [content] };
                },
                
                eventClick: function(info) {
                    Swal.fire({
                        title: info.event.extendedProps.numeroCarpeta,
                        html: `
                            <p><strong>Tipo:</strong> ${info.event.extendedProps.tipoProcedimiento}</p>
                            <hr style="margin: 20px 0; border-top: 3px solid #eee;">
                            <p><strong>Duración:</strong> ${info.event.extendedProps.duracion} minutos</p>
                            <p><strong>Fecha:</strong> ${info.event.start.toLocaleDateString('es-ES', {year: 'numeric', month: '2-digit', day: '2-digit'})}</p>
                            <p><strong>Hora:</strong> ${info.event.start.toLocaleTimeString('es-ES', {hour: '2-digit', minute:'2-digit'})}</p>
                            <hr style="margin: 20px 0; border-top: 3px solid #eee;">
                            <p><strong>Espacio:</strong> ${info.event.extendedProps.sala}</p>
                            <p><strong>Juzgado:</strong> ${info.event.extendedProps.juzgado}</p>
                            
                            <hr style="margin: 20px 0; border-top: 3px solid #eee;">
                           
                            
                            
                            
                        `,
                        icon: 'info',
                        confirmButtonText: 'Cerrar',
                        confirmButtonColor: '#fc4848',
                        width: '600px'
                    });
                }
            });
            
            calendar.render();
            // Asignar el objeto calendar a una variable global o accesible para el formulario
            window.calendar = calendar;
            

            document.getElementById('salaFilter').addEventListener('change', function() {
                calendar.refetchEvents();
            });

            // Nuevo: Event listener para el campo de fecha del formulario
            document.getElementById('fecha').addEventListener('change', function() {
                const selectedDate = this.value; // Obtiene la fecha seleccionada en formato YYYY-MM-DD
                if (selectedDate) {
                    calendar.gotoDate(selectedDate); // Mueve el calendario a la fecha seleccionada
                }
            });
        });

        // Nuevo: Event listener para el campo de hora y duracion del formulario
        document.getElementById('hora').addEventListener('change', function() {
            const selectedTime = this.value; // Obtiene la hora seleccionada en formato HH:MM
            if (selectedTime) {
                const fechaInput = document.getElementById('fecha');
                const selectedDate = fechaInput.value; // Obtiene la fecha seleccionada
                if (selectedDate) {
                     
                }
            }
        });


        let reservaPreview = null;

// Función para actualizar el preview
function updateReservaPreview() {
    const fecha = document.getElementById('fecha').value;
    const hora = document.getElementById('hora').value;
    const duracion = document.getElementById('duracion').value;
    
    // Validar datos completos
    if (!fecha || !hora || !duracion) {
        if (reservaPreview) {
            reservaPreview.remove();
            reservaPreview = null;
        }
        return;
    }
    
    // Eliminar preview anterior si existe
    if (reservaPreview) {
        reservaPreview.remove();
    }
    
    // Crear nuevo preview
    const start = `${fecha}T${hora}`;
    const end = new Date(new Date(start).getTime() + duracion * 60000).toISOString();
    
    reservaPreview = calendar.addEvent({
        id: 'preview-reserva',
        title: `Previsualización (${duracion} min)`,
        start: start,
        end: end,
        color: '#6c757d',
        display: 'auto',
        extendedProps: {
            isPreview: true,
            numeroCarpeta: 'Vista Previa de Reserva Actual',
            tipoProcedimiento: 'Previsualización',
            duracion: duracion,
            sala: 'Previsualización',
            juzgado: 'Previsualización'
        },
        className: 'preview-event'
    });
}



// Event listeners para hora y duración
document.getElementById('hora').addEventListener('change', updateReservaPreview);
document.getElementById('duracion').addEventListener('input', updateReservaPreview);




document.getElementById('hora').addEventListener('change', function() {
    const horaSeleccionada = this.value; // Formato HH:MM
    const [horas, minutos] = horaSeleccionada.split(':').map(Number);
    
    // Convertir a minutos desde medianoche para fácil comparación
    const minutosTotales = horas * 60 + minutos;
    const minHoraLaboral = 8 * 60;  // 8:00 AM (480 minutos)
    const maxHoraLaboral = 18 * 60;  // 6:00 PM (1080 minutos)
    
    if (minutosTotales < minHoraLaboral || minutosTotales > maxHoraLaboral) {
        Swal.fire({
            icon: 'error',
            title: 'Horario no disponible',
            html: '⏰ El horario de atención es de <b>8:00 AM a 6:00 PM</b><br><br>Por favor seleccione un horario dentro de este rango',
            confirmButtonColor: '#fc4848'
        });
        this.value = ''; // Limpiar el campo
        this.focus(); // Regresar el foco al campo
    } else {
        updateReservaPreview(); // Actualizar previsualización si es válido
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const fechaInput = document.getElementById('fecha');
    const today = new Date().toISOString().split('T')[0];
    
    // Si hay una fecha establecida y es anterior a hoy
    if (fechaInput.value && fechaInput.value < today) {
        fechaInput.readOnly = true;
    }
    
    // Establecer fecha mínima como hoy para evitar seleccionar fechas pasadas
    fechaInput.min = today;
});

        
        
        const style = document.createElement('style');
        style.textContent = `
            
            
            #event-tooltip {
                position: fixed;
                display: none;
                background-color: rgba(0, 0, 0, 0.9);
                color: white;
                padding: 12px;
                border-radius: 6px;
                max-width: 300px;
                z-index: 10000;
                pointer-events: none;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                font-size: 14px;
                line-height: 1.5;
                transform: translateX(-50%) translateY(-100%);
                margin-top: -15px;
            }
            
            #event-tooltip:after {
                content: '';
                position: absolute;
                bottom: -10px;
                left: 50%;
                transform: translateX(-50%);
                border-width: 10px 10px 0;
                border-style: solid;
                border-color: rgba(0, 0, 0, 0.9) transparent transparent;
            }
            
            #event-tooltip .tooltip-content {
                text-align: left;
            }
            
            #event-tooltip strong {
                color:rgb(250, 248, 248);
                font-weight: 600;
            }
            
            #event-tooltip em {
                color: #adb5bd;
                font-size: 12px;
                display: block;
                margin-top: 8px;
                font-style: italic;
            }
            
            /* Estilo para eventos en vista mes */
            .fc-dayGridMonth-view .fc-event {
                margin-bottom: 2px;
            }
            
            /* Estilo para la vista lista */
            .fc-listDay-view .fc-event {
                background-color: transparent;
                border: none;
                color: inherit;
            }
            
            .fc-listDay-view .fc-event:hover {
                background-color: rgba(0, 0, 0, 0.05);
            }
            
            /* Colores para juzgados */
            .juzgado-1 {
                background-color: #afda8c !important;
                border-color: #afda8c !important;
            }

            .juzgado-2 {
                background-color: #8cd6da !important;
                border-color: #8cd6da !important;
            }

            .juzgado-3 {
                background-color: #b78cda !important;
                border-color: #b78cda !important;
            }

            .juzgado-4 {
                background-color: #da908c !important;
                border-color: #da908c !important;
            }

            .juzgado-6 {
                background-color: #FFEEAD !important;
                border-color: #FFEEAD !important;
            }

            /* Agrega esto al final de tu estilo */
            .form-select {
                transition: all 0.3s ease;
                border: 2px solid #dee2e6;
            }

            .form-select:focus {
                border-color: #86b7fe;
                box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
            }
            /* Asegurar que el calendario ocupe el 100% del alto de su contenedor */
            #calendar {
                height: 100%; /* Ocupa todo el alto disponible en su columna */
            }
            .fc .fc-view-harness {
                height: 100%; /* Asegura que el harness de FullCalendar ocupe el 100% */
            }
            .fc .fc-view-harness-active {
                flex-grow: 1; /* Permite que el harness crezca para llenar el espacio */
            }
            .card.shadow.p-4.text-dark.h-100 {
                display: flex;
                flex-direction: column;
            }
        `;
        document.head.appendChild(style);

    </script>
</body>