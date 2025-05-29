<?php
include("../models/conexion.php");

try {
    $sql = "SELECT 
                r.ID AS id,
                r.NumeroCarpeta,
                r.TipoProcedimiento,
                r.Fecha, 
                r.Hora, 
                r.Duracion,
                r.cargo,
                r.Nombre,
                r.Juzgado,
                r.Sala,
                r.Estado,
                r.Observaciones
            FROM Reservas r
            JOIN usuarios u ON r.usuario_id = u.id"; // Asumiendo que reservas.usuario_id relaciona con usuarios.id
    
    $stmt = $db->prepare($sql);
    $stmt->execute();

    $citas = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $start = new DateTime($row['Fecha'] . ' ' . $row['Hora']);
        $duracion = $row['Duracion'] ?? 30;
        
        $citas[] = [
            'id' => $row['id'],
      
            'numeroCarpeta' => $row['NumeroCarpeta'],
            'juzgado' => $row['Juzgado'],
            'start' => $start->format('Y-m-d\TH:i:s'),
            'end' => $start->modify("+{$duracion} minutes")->format('Y-m-d\TH:i:s'),
            'extendedProps' => [
                'cargo' => $row['cargo'],
                'duracion' => $duracion,
                'tipoProcedimiento' => $row['TipoProcedimiento'],
                'sala' => $row['Sala'],
                'estado' => $row['Estado'],
                'observaciones' => $row['Observaciones'],
                
                'fecha' => $row['Fecha']
            ],
            'className' => 'juzgado-' . preg_replace('/\D/', '', $row['Juzgado'])
        ];
    }
    
    $citas_json = json_encode($citas, JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    error_log("Error en la base de datos: " . $e->getMessage());
    $citas_json = '[]';
}

$db = null;
?>



<div id='calendar' class='text-dark '></div>
<script>
    window.allEvents = <?php echo $citas_json; ?>;
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    
   
document.addEventListener('DOMContentLoaded', function() {
    const tooltip = document.createElement('div');
    tooltip.id = 'event-tooltip';
    tooltip.style.display = 'none';
    document.body.appendChild(tooltip);
    

    var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
        initialView: 'dayGridMonth',
        locale: 'es',
        slotMinTime: '08:00:00',
        slotMaxTime: '18:00:00',
        headerToolbar: {
            center: 'title',
            left: 'prev,next today',
      
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
        
        // Solo activar tooltip para vistas que no sean listDay
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
                    <p><strong>Fecha:</strong> ${info.event.extendedProps.fecha}</p>
                    <p><strong>Hora:</strong> ${info.event.start.toLocaleTimeString('es-ES')}</p>
                    <hr style="margin: 20px 0; border-top: 3px solid #eee;">
                    <p><strong>Espacio:</strong> ${info.event.extendedProps.sala}</p>
                    <p><strong>Juzgado:</strong> ${info.event.extendedProps.juzgado}</p>
                    <p><strong>Estado:</strong> <span class="badge text-light ${getEstadoClass(info.event.extendedProps.estado)}">
                    ${info.event.extendedProps.estado}
                    </span></p>
                    <hr style="margin: 20px 0; border-top: 3px solid #eee;">

                `,
                icon: 'info',
                confirmButtonText: 'Cerrar',
                confirmButtonColor: '#fc4848',
                width: '600px'
            });
        }
    });
    function getEstadoClass(estado) {
        const clases = {
            'Pendiente': 'bg-warning',
            'Confirmado': 'bg-success',
            'Cancelado': 'bg-danger'
        };
        return clases[estado] || 'bg-secondary';
    }

    
    calendar.render();
      document.getElementById('salaFilter').addEventListener('change', function() {
        calendar.refetchEvents();
    });
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

    
    `;
document.head.appendChild(style);

</script>