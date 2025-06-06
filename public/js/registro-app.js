$(document).ready(function() {
    // 1. Inicialización de DataTable
    $('#tablaReservas').DataTable({
       

        "language": {
            "lengthMenu": "Mostrar _MENU_ registros por página",
            "zeroRecords": "No se encontraron resultados",
            "info": "Mostrando página _PAGE_ de _PAGES_",
            "infoEmpty": "No hay registros disponibles",
            "infoFiltered": "(filtrado de _MAX_ registros totales)",
            "search": "Buscar:",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
            }
        },
    "dom": '<"top"lf>rt<"bottom"ip>',
    "initComplete": function(settings, json) {
        // Agregar atributos al campo de búsqueda
        $('.dataTables_filter input')
            .attr('id', 'tablaSearch')
            .attr('name', 'search');
    }
});



    // 2. Manejo del formulario de reserva
    $('#formReserva').on('submit', function(e) {
    e.preventDefault();
    var btn = $('#btnSubmit');
    var form = $(this);
    
    // Mostrar loading
    btn.prop('disabled', true).text('Procesando...');
    
    // Formatear fecha correctamente antes de enviar (para el formato MySQL)
    let fechaInput = $('#fecha_hora').val();
    let fechaFormateada = fechaInput.replace('T', ' ') + ':00';
    
    $.ajax({
        url: '../controllers/insert',
        type: 'POST',
        data: form.serialize() + '&fecha_hora_formatted=' + encodeURIComponent(fechaFormateada),
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                // Mostrar alerta de éxito con detalles
                alert('✅ RESERVA EXITOSA\n\n' +
                     'Número: EXP' + response.data.numero_carpeta + '\n' +
                     'Fecha: ' + formatDate(response.data.fecha_hora) + '\n' +
                     'Tipo: ' + response.data.tipo);
                
                // Cerrar modal y recargar
                $('#modalReserva').modal('hide');
                setTimeout(function() {
                    location.reload();
                }, 1500);
            } else {
                // Mostrar errores de validación
                if(response.errors) {
                    alert('❌ ERRORES:\n\n- ' + response.errors.join('\n- '));
                } else {
                    alert('❌ ERROR: ' + response.message);
                }
            }
        },
        error: function(xhr) {
            alert('⚠️ ERROR DE CONEXIÓN: ' + xhr.statusText);
        },
        complete: function() {
            btn.prop('disabled', false).text('Enviar');
        }
    });
});

// Función para formatear fecha legible
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}
    

    // 3. Manejo de eliminación

    $('.btn-eliminar').on('click', function(e) {
    e.preventDefault();
    const id = $(this).data('id');
    
    if (!confirm('⚠️ ¿Seguro que deseas eliminar esta reserva?')) return;
    
    $.ajax({
        url: '../controllers/delete_reserva',
        method: 'POST',
        data: { id: id },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert(response.message);
                location.reload();
            } else {
                alert(' ❌ Error: ' + response.message);
            }
        },
        error: function(xhr) {
            alert(' ❌ Error de conexión: ' + xhr.responseText);
        }
    });
});
});

