<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../views/login.php");
    exit();
}
?>
 <?php
    require_once 'inc/header.php'; 
    require_once 'inc/navbar_app.php'; 

    include '../models/conexion.php';
    $sentencia = $db->query("SELECT * FROM reservas;");
    $dato = $sentencia->fetchAll(PDO::FETCH_OBJ);
    ?>


<body>
    <div class="container-fluid">
        <div class="row">
            <div class="container-fluid col-md-12 col-lg-10 mx-auto mt-4 mb-5">
                <div class="card-header text-center text-dark">
                    <h1 class="h3 mb-0" style="font-size: calc(1.2rem + 0.6vw)">Registrar Reservaciones</h1>
                </div>
                <br>
                
                <div class="">
                    <a href="modals/registrar_reserva.php" class="btn btn-outline-success border">
                       <i class="fas fa-calendar"></i> Iniciar reserva
                    </a>
                </div>
                

                <hr>
                
                
                <table class="table table-striped mb-0" id="tablaReservas">
                    <thead class="table-dark ">
                        <tr>
                        <th>JUZGADO</th>
                            <th>N° CARPETA</th>
                            <th>FECHA</th>
                            <th>HORA</th>
                            <th>DURACIÓN</th>
                            <th>TIPO</th>
                            <th>PUESTO</th>
                            <th>OBSERVACIONES</th>
                            <th>MOTIVO</th>
                            <th>ESTADO</th>
                            <th>ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody >
                        <?php foreach ($dato as $registro) { ?>
                            <tr>
                            <td><?php echo $registro->Juzgado; ?></td>
                                <td><?php echo $registro->numeroCarpeta; ?></td>
                                <td><?php echo $registro->Fecha; ?></td>
                                
                                <td><?php echo $registro->Hora; ?></td>
                                <td><?php echo $registro->Duracion; ?></td>
                                <td><?php echo $registro->TipoProcedimiento; ?></td>

                                <td><?php echo $registro->Puesto; ?></td>
                                <td><?php echo substr($registro->Observaciones, 0, 30) . (strlen($registro->Observaciones) > 30 ? '...' : ''); ?></td>
                                <td><?php echo $registro->Motivo; ?></td>
                                
                                <td>
                                    <?php
                                    $estado = $registro->Estado;
                                    $clase_color = '';
                                    switch ($estado) {
                                        case 'Pendiente': $clase_color = 'bg-warning text-white shadow-warning'; break;
                                        case 'Cancelado': $clase_color = 'bg-danger text-white shadow-danger'; break;
                                        case 'Confirmado': $clase_color = 'bg-success text-white shadow-success'; break;
                                        default: $clase_color = ''; break;
                                    }
                                    ?>
                                    <span class="badge <?php echo $clase_color; ?>">
                                        <?php echo $estado; ?>
                                    </span>
                                </td>
                                <td>
                                <div class="d-flex">
                                        <a href="modals/editar_reserva.php?id=<?php echo $registro->ID; ?>" 
                                        class="btn btn-warning btn-sm me-2" 
                                        title="Editar esta reserva">
                                             Editar
                                        </a>
                                        <div style="border: .1px solid white; height: 30px; margin: 0 1px;"></div>
                                        <button class="btn btn-danger btn-eliminar btn-sm" data-id="<?php echo $registro->ID; ?>">
                                            Eliminar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                
            </div>
        </div>
    </div>

    <?php include("inc/footerq.php"); ?>






















    <!-- Cambio 2: Script actualizado (solo este bloque se modifica) -->
    <script>
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
        url: '../controllers/insert.php',
        type: 'POST',
        data: form.serialize() + '&fecha_hora_formatted=' + encodeURIComponent(fechaFormateada),
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                // Mostrar alerta de éxito con detalles
                alert('✅ RESERVA EXITOSA\n\n' +
                     'Número: EXPEDIENTE-' + response.data.numero_carpeta + '\n' +
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
        url: '../controllers/delete_reserva.php',
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



</script>
</body>
</html>

