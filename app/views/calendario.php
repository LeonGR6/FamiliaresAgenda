<?php
// Incluir archivo de configuración de la base de datos
include("../models/conexion.php");

try {
    // Consulta SQL modificada para incluir duración
    $sql = "SELECT 
                ID AS id, 
                Motivo AS title, 
                NumeroCarpeta,
                TipoProcedimiento,
                Fecha, 
                Hora, 
                Duracion,
                Puesto,
                Juzgado
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
            'title' => $row['title'],
            'numeroCarpeta' => $row['NumeroCarpeta'], // Asegúrate de incluir este campo
            'juzgado' => $row['Juzgado'],
            'start' => $start->format('Y-m-d\TH:i:s'),
            'end' => $start->modify("+{$duracion} minutes")->format('Y-m-d\TH:i:s'),
            'extendedProps' => [
                'puesto' => $row['Puesto'],
                'duracion' => $duracion,
                'tipoProcedimiento' => $row['TipoProcedimiento']
            ],
            'className' => 'duracion-' . $duracion // Clase CSS para la duración
        ];
    }
    
    $citas_json = json_encode($citas);
} catch (PDOException $e) {
    error_log("Error en la base de datos: " . $e->getMessage());
    $citas_json = '[]';
}

$db = null;
?>



<div id='calendar' class='text-dark '></div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    
document.addEventListener('DOMContentLoaded', function() {
    const tooltip = document.createElement('div');
    tooltip.id = 'event-tooltip';
    tooltip.style.display = 'none';
    document.body.appendChild(tooltip);

    var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
        initialView: 'timeGridWeek',
        locale: 'es',
        slotMinTime: '08:00:00',
        slotMaxTime: '18:00:00',
        
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
        events: <?php echo $citas_json; ?>,
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
                    <div style="
                        white-space: nowrap;
                        overflow: hidden;
                        text-overflow: ellipsis;
                        font-size: 0.9em;
                    ">
                        ${arg.timeText} ${arg.event.title}
                    </div>
                `;
            } else if (arg.view.type === 'listDay') {
                content.innerHTML = `
                    <div style="display: flex; justify-content: space-between;">
                        <div>${arg.timeText}</div>
                        <div><strong>${arg.event.title}</strong></div>
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
                    <p><strong>Motivo:</strong> ${info.event.title}</p>
                    <p><strong>Juzgado:</strong> ${info.event.extendedProps.juzgado}</p>
                    <hr>
                    <p><strong>Hora:</strong> ${info.event.start.toLocaleTimeString('es-ES')}</p>
                    <p><strong>Duración:</strong> ${info.event.extendedProps.duracion} minutos</p>
                    <p><strong>Puesto:</strong> ${info.event.extendedProps.puesto}</p>
                `,
                icon: 'info',
                confirmButtonText: 'Cerrar',
                confirmButtonColor: '#fc4848',
                width: '600px'
            });
        }
    });
    
    calendar.render();
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
        color: #f8f9fa;
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
`;
document.head.appendChild(style);

</script>