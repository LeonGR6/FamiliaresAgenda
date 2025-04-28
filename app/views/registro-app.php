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
                <?php include "modals/reserva.php"; ?>
                <?php include "modals/editar.php"; ?>
                <hr>
                <table class="table table-striped " id="tablaReservas" >
                    <!-- Tabla se mantiene exactamente igual -->
                    <thead class="table-dark">
                        <tr>
                            <th>NOMBRE</th>
                            <th>APELLIDOS</th>
                            <th>EMAIL</th>
                            <th>SERVICIO</th>
                            <th>FECHA</th>
                            <th>HORA</th>
                            <th>MENSAJE</th>
                            <th>ESTADO</th>
                            <th>ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dato as $registro) { ?>
                            <tr>
                                <td><?php echo $registro->Nombre; ?></td>
                                <td><?php echo $registro->Apellidos; ?></td>
                                <td><?php echo $registro->Correo; ?></td>
                                <td><?php echo $registro->Servicio; ?></td>
                                <td><?php echo $registro->Fecha; ?></td>
                                <td><?php echo $registro->Hora; ?></td>
                                <td><?php echo $registro->MensajeAdicional; ?></td>
                                <td>
                                    <?php
                                    $estado = $registro->Estado;
                                    $clase_color = '';
                                    switch ($estado) {
                                        case 'Pendiente': $clase_color = 'text-warning'; break;
                                        case 'Cancelado': $clase_color = 'text-danger'; break;
                                        case 'Confirmado': $clase_color = 'text-success'; break;
                                        default: $clase_color = ''; break;
                                    }
                                    ?>
                                    <b class="<?php echo $clase_color; ?>"><?php echo $estado; ?></b>
                                </td>
                                <td>
                                    <div class="d-flex">
                                    <button class="btn btn-warning btn-editar btn-sm" data-id="<?php echo $registro->ID; ?>">
                                            Editar
                                        </button>
                                        <!-- Cambio 1: Añadí data-id al botón eliminar -->
                                       
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
        
        $.ajax({
            url: '../controllers/insert.php', // Tu ruta actual
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                if(response && response.success) {
                    // Mostrar alerta de éxito
                    alert('✅ Reserva registrada con éxito\n\n' +
                         'Nombre: ' + response.data.nombre + ' ' + response.data.apellidos + '\n' +
                         'Fecha: ' + response.data.fecha + '\n' +
                         'Hora: ' + response.data.hora);
                    
                    // Cerrar modal y recargar
                    $('#modalReserva').modal('hide');
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    alert('❌ Error: ' + (response && response.message || 'Error al procesar la reserva'));
                }
            },
            error: function(xhr) {
                alert('❌ Error de conexión: ' + xhr.statusText);
            },
            complete: function() {
                btn.prop('disabled', false).text('Enviar');
            }
        });
    });

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
// 4. Manejo del botón editar - Cargar datos
$(document).on('click', '.btn-editar', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        
        // Mostrar spinner mientras carga
        $('#modalEditarReserva .modal-body').html(`
            <div class="text-center py-4">
                <div class="spinner-border text-warning" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-2">Cargando datos de la reserva...</p>
            </div>
        `);
        
        $('#modalEditarReserva').modal('show');
        
        // Obtener datos via AJAX
        $.ajax({
            url: '../controllers/get_reserva.php',
            type: 'GET',
            data: { id: id },
            dataType: 'json',
            success: function(reserva) {
                // Llenar formulario
                $('#reservaIdHeader').text(reserva.ID);
                $('#edit_id').val(reserva.ID);
                $('#edit_nombre').val(reserva.Nombre);
                $('#edit_apellidos').val(reserva.Apellidos);
                $('#edit_correo').val(reserva.Correo);
                $('#edit_servicio').val(reserva.Servicio);
                $('#edit_fecha').val(reserva.Fecha);
                $('#edit_hora').val(reserva.Hora);
                $('#edit_mensaje').val(reserva.MensajeAdicional);
                $('#edit_estado').val(reserva.Estado);
                
                // Mostrar formulario completo
                $('#modalEditarReserva .modal-body').html(`
                    <input type="hidden" id="edit_id" name="id" value="${reserva.ID}">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="edit_nombre" class="form-label">Nombre</label>
                            <input type="text" id="edit_nombre" name="nombre" class="form-control" value="${reserva.Nombre}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_apellidos" class="form-label">Apellidos</label>
                            <input type="text" id="edit_apellidos" name="apellidos" class="form-control" value="${reserva.Apellidos}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_correo" class="form-label">Correo</label>
                            <input type="email" id="edit_correo" name="correo" class="form-control" value="${reserva.Correo}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_servicio" class="form-label">Servicio</label>
                            <input type="text" id="edit_servicio" name="servicio" class="form-control" value="${reserva.Servicio}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_fecha" class="form-label">Fecha</label>
                            <input type="date" id="edit_fecha" name="fecha" class="form-control" value="${reserva.Fecha}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_hora" class="form-label">Hora</label>
                            <input type="time" id="edit_hora" name="hora" class="form-control" value="${reserva.Hora}" required>
                        </div>
                        <div class="col-12">
                            <label for="edit_mensaje" class="form-label">Mensaje Adicional</label>
                            <textarea id="edit_mensaje" name="mensaje" class="form-control" rows="3">${reserva.MensajeAdicional || ''}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_estado" class="form-label">Estado</label>
                            <select id="edit_estado" name="estado" class="form-select" required>
                                <option value="Pendiente" ${reserva.Estado === 'Pendiente' ? 'selected' : ''}>Pendiente</option>
                                <option value="Confirmado" ${reserva.Estado === 'Confirmado' ? 'selected' : ''}>Confirmado</option>
                                <option value="Cancelado" ${reserva.Estado === 'Cancelado' ? 'selected' : ''}>Cancelado</option>
                            </select>
                        </div>
                    </div>
                `);
            },
            error: function(xhr) {
                console.error('Error en AJAX:', xhr.responseText);
                $('#modalEditarReserva').modal('hide');
                alert('Error al cargar los datos. Consola para más detalles.');
            }
        });
    });

    // 5. Manejo del envío del formulario de edición
    $(document).on('submit', '#formEditarReserva', function(e) {
        e.preventDefault();
        const formData = $(this).serialize();
        const btn = $(this).find('button[type="submit"]');
        
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');
        
        $.ajax({
            url: '../controllers/update_reserva.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    alert('✅ Reserva actualizada con éxito');
                    $('#modalEditarReserva').modal('hide');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    alert('❌ Error: ' + (response.message || 'Error al actualizar'));
                }
            },
            error: function(xhr) {
                alert('❌ Error de conexión: ' + xhr.statusText);
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="fas fa-save"></i> Guardar');
            }
        });
    });


</script>
</body>
</html>

