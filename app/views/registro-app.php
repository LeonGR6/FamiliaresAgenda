<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../views/login.php");
    exit();
}

require_once 'inc/header.php'; 
require_once 'inc/navbar_app.php'; 
include '../models/conexion.php';

// CONSULTA CON MANEJO DE ERRORES
try {
    $sentencia = $db->query("SELECT 
        r.ID, 
        r.numeroCarpeta, 
        r.Juzgado, 
        r.Fecha, 
        TIME_FORMAT(r.Hora, '%H:%i') as Hora,
        r.Sala, 
        r.Duracion,
        r.TipoProcedimiento, 
        r.Cargo, 
        r.Nombre,
        r.Observaciones,
        r.Estado,
        r.usuario_id,
        u.cargo
    FROM reservas r
    JOIN usuarios u ON r.usuario_id = u.id");
    
    $dato = $sentencia->fetchAll(PDO::FETCH_OBJ);
} catch(PDOException $e) {
    die("Error al cargar reservas: " . $e->getMessage());
}
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
                            <th>N° EXPEDIENTE</th>
                            <th>FECHA</th>
                            <th>ESPACIO</th>
                            <th>HORA</th>
                            <th>DURACIÓN</th>
                            <th>CARGO</th>
                            <th>NOMBRE</th>
                            <th>TIPO</th>
                            
                            <th>OBSERVACIONES</th>
                            
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
                                <td><?php echo $registro->Sala; ?></td>
                                <td><?php echo $registro->Hora; ?></td>
                                <td><?php echo $registro->Duracion; ?></td>
                                <td><?php echo $registro->Cargo; ?></td>
                                <td><?php echo $registro->Nombre; ?></td>
                                <td><?php echo $registro->TipoProcedimiento; ?></td>

                                
                                <td><?php echo substr($registro->Observaciones, 0, 30) . (strlen($registro->Observaciones) > 30 ? '...' : ''); ?></td>
                                
                                
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
                                        <button class="btn btn-primary btn-ver btn-sm" 
                                                data-id="<?php echo $registro->ID; ?>"
                                                data-numero-carpeta="<?php echo $registro->numeroCarpeta; ?>"
                                                data-juzgado="<?php echo $registro->Juzgado; ?>"
                                                data-fecha="<?php echo $registro->Fecha; ?>"
                                                data-hora="<?php echo $registro->Hora; ?>"
                                                data-sala="<?php echo $registro->Sala; ?>"
                                                data-duracion="<?php echo $registro->Duracion; ?>"
                                                data-cargo="<?php echo $registro->cargo ?? 'Espectador'; ?>"
                                                data-tipo="<?php echo $registro->TipoProcedimiento; ?>"
                                                
                                                data-observaciones="<?php echo htmlspecialchars($registro->Observaciones ?? ''); ?>">
                                            <i class=""></i> Ver
                                        </button>

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
    <script src="/mvc-php/public/js/registro-app.js"></script>

    <script>
 $(document).ready(function() {
        $(document).on('click', '.btn-ver', function() {
            const registro = $(this).data();
            
            Swal.fire({
                title: `<br> 📋 Detalles de Reserva (ID: ${registro.id})`, 
                
                html: ` <hr style="margin: 20px 0; border-top: 3px solid #eee;"> <br> 
                    <div style="text-align: center;">
                        <p><strong>Expediente:</strong> ${registro.numeroCarpeta || 'No especificado'}</p>
                        <p><strong>Tipo:</strong> ${registro.tipo}</p>
                        <p><strong>Duración:</strong> ${registro.duracion} minutos</p>
                        <hr style="margin: 20px 0; border-top: 3px solid #eee;">
                        <p><strong>Fecha:</strong> ${registro.fecha}</p>
                        <p><strong>Hora:</strong> ${registro.hora}</p>
                        
                        <p><strong>Espacio:</strong> ${registro.sala}</p>
                        <p><strong>Juzgado:</strong> ${registro.juzgado}</p>
                        <hr style="margin: 20px 0; border-top: 3px solid #eee;">
                        <p><strong>Cargo:</strong> ${registro.cargo || 'Espectador'}</p>
                        
                        <p><strong>Observaciones:</strong></p>
                        <div >
                            ${registro.observaciones || 'Ninguna'}
                        </div>
                    </div>
                `,
                
                confirmButtonText: 'Cerrar',
                confirmButtonColor: '#fc4848',
                width: '600px'
                
        });
    });
});
</script>
</body>
</html>

