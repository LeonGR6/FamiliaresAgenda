<?php
// ==================== CONTROLADOR ====================
session_start();

// Verificación de autenticación
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../views/login.php");
    exit();
}

require_once '../../models/conexion.php';

// 1. Validación del ID de reserva
$id_reserva = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id_reserva) {
    echo "<script>alert('⚠️ ID de reserva inválido o no proporcionado'); window.location.href = '../registro-app.php';</script>";
    exit();
}

// 2. Obtener datos de la reserva
try {
    $sentencia = $db->prepare("SELECT * FROM reservas WHERE ID = ?");
    $sentencia->execute([$id_reserva]);
    $reserva = $sentencia->fetch(PDO::FETCH_OBJ);

    if (!$reserva) {
        echo "<script>alert('⚠️ Reserva no encontrada'); window.location.href = '../registro-app.php';</script>";
        exit();
    }
} catch (PDOException $e) {
    echo "<script>alert('❌ Error al obtener reserva: " . addslashes($e->getMessage()) . "'); window.location.href = '../registro-app.php';</script>";
    exit();
}

// 3. Procesamiento del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validación de campos requeridos
        $campos_requeridos = [
            'numero_carpeta' => "Número de carpeta",
            'fecha' => "Fecha",
            'hora' => "Hora",
            'tipo' => "Tipo de procedimiento",
            'juzgado' => 'Juzgado',
            'sala' => 'Sala',
            'duracion' => 'Duración'
        ];
        
        foreach ($campos_requeridos as $campo => $nombre) {
            if (empty($_POST[$campo])) {
                throw new Exception("El campo {$nombre} es requerido");
            }
        }

        // Sanitización y formato de datos
        $sala = mb_strtoupper(trim($_POST['sala']));
        
            // Si el valor de sala no está vacío y ya contiene "SALA" o "CAMARA" seguido de un número
    if (!empty($sala) && preg_match('/^(SALA|CAMARA)\s\d+$/i', $sala)) {
        // Mantener el valor existente sin cambios
        $sala = $sala;
    } 
    // Si está vacío o no cumple el formato, aplicar la lógica de asignación aleatoria
    else {
        // Validar tipo de espacio
        if ($sala !== 'SALA' && $sala !== 'CAMARA') {
            throw new Exception("❌ Tipo de espacio inválido. Elige 'SALA' o 'CAMARA'.");
        }

        // Asignar SALA o CÁMARA aleatoriamente
        if ($sala === 'SALA') {
            $sala = 'SALA ' . rand(1, 3);
        } else {
            $sala = 'CAMARA ' . rand(1, 2);
        }
    }

        $datos = [
            'numeroCarpeta' => '' . preg_replace('/[^0-9-]/', '', $_POST['numero_carpeta']),
            'TipoProcedimiento' => mb_strtoupper(trim($_POST['tipo'])),
            'Fecha' => $_POST['fecha'],
            'Hora' => $_POST['hora'],
            'Juzgado' => mb_strtoupper(trim($_POST['juzgado'])),
            'Sala' => $sala,
            'Cargo' => $reserva->Cargo ?? null,
            'Duracion' => filter_var($_POST['duracion'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 30, 'max_range' => 480]]),
            'Observaciones' => isset($_POST['observaciones']) ? mb_strtoupper(trim($_POST['observaciones'])) : null,
            'Estado' => $_POST['estado'] ?? 'Pendiente',
            'id' => $id_reserva
        ];

        if ($datos['Duracion'] === false) {
            throw new Exception("La duración debe ser un número entre 30 y 480 minutos");
        }

        // Validar fecha no pasada
        

        // Verificar disponibilidad (excluyendo esta reserva)
        $sqlCheck = "SELECT ID FROM reservas 
                    WHERE Fecha = :Fecha
                    AND Sala = :Sala 
                    AND Hora = :Hora 
                    AND ID != :id";
        $stmtCheck = $db->prepare($sqlCheck);
        $stmtCheck->execute([
            'Fecha' => $datos['Fecha'],
            'Sala' => $datos['Sala'],
            'Hora' => $datos['Hora'],
            'id' => $id_reserva
        ]);

        if ($stmtCheck->fetch()) {
            throw new Exception("❌ El juzgado ya tiene una reserva para esa fecha y hora");
        }

        // Verificar solapamiento con otras reservas
        $horaInicio = explode(':', $datos['Hora']);
        $minutosInicio = $horaInicio[0] * 60 + $horaInicio[1];
        $minutosFin = $minutosInicio + $datos['Duracion'];

        // Verificar solapamiento con otras reservas (incluyendo margen de 10 minutos)
            $sqlSolapamiento = "SELECT COUNT(*) as count FROM reservas 
            WHERE Fecha = :Fecha
            AND Sala = :Sala 
            AND ID != :id
            AND (
                /* Reservas existentes que solapan con el inicio de la nueva reserva (incluyendo margen) */
                (Hora <= :Hora AND ADDTIME(Hora, SEC_TO_TIME((Duracion + 10) * 60)) > :Hora)
                OR 
                /* Reservas existentes que solapan con el fin de la nueva reserva (incluyendo margen) */
                (Hora < ADDTIME(:Hora, SEC_TO_TIME((:Duracion + 10) * 60)) 
                AND ADDTIME(Hora, SEC_TO_TIME(Duracion * 60)) >= ADDTIME(:Hora, SEC_TO_TIME(:Duracion * 60)))
            )";

            $stmtSolapamiento = $db->prepare($sqlSolapamiento);
            $stmtSolapamiento->execute([
            'Fecha' => $datos['Fecha'],
            'Sala' => $datos['Sala'],
            'id' => $id_reserva,
            'Hora' => $datos['Hora'],
            'Duracion' => $datos['Duracion']
            ]);

            $result = $stmtSolapamiento->fetch(PDO::FETCH_OBJ);

            if ($result->count > 0) {
            $horaFinConMargen = date('H:i', strtotime("+".($datos['Duracion']+10)." minutes", strtotime($datos['Hora'])));
            $tipoMostrar = (strpos($datos['Sala'], 'SALA') !== false) ? 'salas' : 'cámaras Gesell';

            throw new Exception("No hay {$tipoMostrar} disponibles para el horario solicitado (incluyendo 10 minutos para desocupar el espacio).");
            }

        // Actualización en base de datos
        $sql = "UPDATE reservas SET
                numeroCarpeta = :numeroCarpeta,
                TipoProcedimiento = :TipoProcedimiento,
                Fecha = :Fecha,
                Hora = :Hora,
                Juzgado = :Juzgado,
                sala = :Sala,
                Duracion = :Duracion,
                Cargo = :Cargo,
                Observaciones = :Observaciones,
                Estado = :Estado
                WHERE ID = :id";

        $sentencia = $db->prepare($sql);
        $resultado = $sentencia->execute($datos);

        if ($resultado && $sentencia->rowCount() > 0) {
            echo "<script>
                alert('✅ Reserva actualizada correctamente');
                window.location.href = '../registro-app.php';
            </script>";
            exit();
        } else {
            throw new Exception("No se realizaron cambios en la reserva");
        }
    } catch (Exception $e) {
        echo "<script>alert('" . addslashes($e->getMessage()) . "');</script>";
        // Mantener los datos para repoblar el formulario
        $reserva = (object) array_merge((array) $reserva, $_POST);
    }
}

// ==================== VISTA ====================
require_once '../inc/header.php'; 
require_once '../inc/navbar_default.php';
?>


<body>
    <div class="container-fluid">
        <div class="row">
            <div class="container-fluid col-md-12 col-lg-10 mx-auto mt-4 mb-5">
                <div class="card-header text-center text-dark">
                    <h1 class="h3 mb-0" style="font-size: calc(1.2rem + 0.6vw)">Editar Reservación</h1>
                </div>
                <br>

                <form method="post" class="card shadow mx-auto p-3 text-dark" style="max-width: 700px; margin: 0 auto;">
                    <input type="hidden" name="id" value="<?php echo $reserva->ID; ?>">

                    <?php 
                    $numero_carpeta = str_replace('', '', $reserva->numeroCarpeta);
                    ?>

                    

                    <div class="row mb-3">
                        
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="fecha">Fecha</label>
                            <input type="date" class="form-control text-uppercase" id="fecha" name="fecha"
                            value="<?php echo isset($reserva->Fecha) ? htmlspecialchars($reserva->Fecha) : ''; ?>">
                        </div>
                    </div>



                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="hora">Hora</label>
                                <input type="time" class="form-control" id="hora" name="hora"
                                value="<?php echo htmlspecialchars($reserva->Hora); ?>" step="60" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="duracion">Duración (minutos)</label>
                                <input type="number" class="form-control" id="duracion" name="duracion"
                                value="<?php echo htmlspecialchars($reserva->Duracion); ?>" 
                                    placeholder="Ej: 30" min="30" max="480" step="1" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="numero_carpeta"></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">EXPEDIENTE</span>
                            </div>
                            <input type="text" class="form-control" id="numero_carpeta" name="numero_carpeta" 
                                value="<?php echo htmlspecialchars($numero_carpeta); ?>" 
                                placeholder="1245-2024" pattern="\d{4}-\d{4}" 
                                oninput="formatExp(this)" 
                                maxlength="9" 
                                required 
                                readonly>
                        </div>
                        <small class="form-text text-muted"></small>
                    </div>

                    <div class="form-group mb-3">
                        <label for="tipo">Tipo</label>
                        <select class="form-control" id="tipo" name="tipo" required>
                            <option value="AUDIENCIAS CONCILIACIÓN" <?php echo ($reserva->TipoProcedimiento ?? '') == 'AUDIENCIAS CONCILIACIÓN' ? 'selected' : ''; ?>>CONCILIACIÓN</option>
                            <option value="AUDIENCIAS DESAHOGO DE PRUEBAS" <?php echo ($reserva->TipoProcedimiento ?? '') == 'AUDIENCIAS DESAHOGO DE PRUEBAS' ? 'selected' : ''; ?>>DESAHOGO DE PRUEBA</option>
                            <option value="AUDIENCIAS ESCUCHA DE MENORES" <?php echo ($reserva->TipoProcedimiento ?? '') == 'AUDIENCIAS ESCUCHA DE MENORES' ? 'selected' : ''; ?>>ESCUCHA DE MENORES</option>
                            <option value="AUDIENCIAS ALEGATOS" <?php echo ($reserva->TipoProcedimiento ?? '') == 'AUDIENCIAS ALEGATOS' ? 'selected' : ''; ?>>ALEGATOS</option>
                        </select>
                    </div>

                   

                    <div class="form-group mb-3">
                        <label for="observaciones">Observaciones</label>
                        <textarea class="form-control text-uppercase" id="observaciones" name="observaciones" 
                                rows="2" maxlength="500"><?php echo htmlspecialchars($reserva->Observaciones ?? ''); ?></textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label for="estado">Estado</label>
                        <select class="form-control" id="estado" name="estado" required>
                            <option value="Pendiente" style="background-color:rgba(246, 255, 0, 0.38); font-weight: 600;" <?php echo ($reserva->Estado ?? 'Pendiente') == 'Pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="Confirmado" style="background-color:rgba(21, 255, 9, 0.35); font-weight: 600;" <?php echo ($reserva->Estado ?? 'Pendiente') == 'Confirmado' ? 'selected' : ''; ?>>Confirmado</option>
                            <option value="Cancelado" style="background-color:rgba(255, 9, 9, 0.35); font-weight: 600;" <?php echo ($reserva->Estado ?? 'Pendiente') == 'Cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="cargo">Cargo</label>
                        <input type="text" class="form-control text-uppercase" id="cargo" name="cargo"
                            value="<?php echo isset($reserva->Cargo) ? htmlspecialchars($reserva->Cargo) : ''; ?>" 
                            readonly>
                    </div>

                    

                    <div class="form-group mb-4">
                        <label for="juzgado">Juzgado</label>
                        <select class="form-control" id="juzgado" name="juzgado" required>
                            <option value="1° F" <?php echo ($reserva->Juzgado ?? '') == '1° F' ? 'selected' : ''; ?>>1° FAMILIARES</option>
                            <option value="2° F" <?php echo ($reserva->Juzgado ?? '') == '2° F' ? 'selected' : ''; ?>>2° FAMILIARES</option>
                            <option value="3° F" <?php echo ($reserva->Juzgado ?? '') == '3° F' ? 'selected' : ''; ?>>3° FAMILIARES</option>
                            <option value="4° F" <?php echo ($reserva->Juzgado ?? '') == '4° F' ? 'selected' : ''; ?>>4° FAMILIARES</option>
                      
                            <option value="6° F" <?php echo ($reserva->Juzgado ?? '') == '6° F' ? 'selected' : ''; ?>>6° FAMILIARES</option>
                        </select>
                    </div>

                    <div class="form-group mb-4">
                        <label for="sala">Sala</label>
                        <input type="text" class="form-control text-uppercase" id="sala" name="sala"
                            placeholder="SOLICITUD DE REVISIÓN"
                            value="<?php echo isset($reserva->Sala) ? htmlspecialchars($reserva->Sala) : ''; ?>" readonly>
                </div>
                        
                    <div class="text-start mt-4">
                        <a href="../registro-app.php" class="btn btn-danger px-4 me-2">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4 me-2">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php require_once '../inc/footerq.php';?>

    <script>
    // Mostrar mensaje si viene en la URL
    const urlParams = new URLSearchParams(window.location.search);
    const message = urlParams.get('message');
    
    if (message) {
        alert(decodeURIComponent(message));
    }

    // Solución para Chrome/Edge (elimina segundos al seleccionar)
    document.getElementById('hora').addEventListener('change', function() {
        if(this.value.length > 5) {
            this.value = this.value.substring(0, 5);
        }
    });

    document.getElementById('fecha').addEventListener('change', function() {
    // Obtener el valor del input de fecha (formato YYYY-MM-DD)
    const fechaInput = this.value;
    
    if (!fechaInput) return; // Si no hay fecha seleccionada, salir
    
    // Crear fechas sin considerar la zona horaria
    const fechaSeleccionada = new Date(fechaInput + 'T00:00:00');
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0); // Normalizar a medianoche
    
    // Comparar las fechas
    if (fechaSeleccionada < hoy) {
        Swal.fire({
            icon: 'error',
            title: 'Fecha inválida',
            html: `Has seleccionado <b>${formatDate(fechaSeleccionada)}</b><br>
                   La fecha no puede ser anterior a <b>${formatDate(hoy)}</b>`,
            confirmButtonColor: '#fc4848'
        });
        this.value = '';
        this.focus();
    }
});

// Función auxiliar para formatear fechas consistentemente
function formatDate(date) {
    return date.toLocaleDateString('es-MX', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

// Validación de hora (dentro del horario laboral 8:00 AM a 6:00 PM)
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
    }
});

 // Si la fecha seleccionada es hoy, validar que no sea hora pasada


    // Establecer la fecha mínima como hoy



// Función para obtener la fecha actual en formato YYYY-MM-DD
function getCurrentDate() {
    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}
// dia anterior no editar, dia actual visible a editar

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
    </script>
</body>
</html>