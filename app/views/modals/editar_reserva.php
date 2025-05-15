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
            'puesto' => "Puesto",
            'juzgado' => 'Juzgado',
            'duracion' => 'Duración'
        ];
        
        foreach ($campos_requeridos as $campo => $nombre) {
            if (empty($_POST[$campo])) {
                throw new Exception("El campo {$nombre} es requerido");
            }
        }

        // Sanitización y formato de datos
        $datos = [
            'numeroCarpeta' => 'EXPEDIENTE-' . preg_replace('/[^0-9-]/', '', $_POST['numero_carpeta']),
            'TipoProcedimiento' => mb_strtoupper(trim($_POST['tipo'])),
            'Fecha' => $_POST['fecha'],
            'Hora' => $_POST['hora'],
            'Juzgado' => mb_strtoupper(trim($_POST['juzgado'])),
            'Duracion' => filter_var($_POST['duracion'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 30]]),
            'Puesto' => mb_strtoupper(trim($_POST['puesto'])),
            'Motivo' => isset($_POST['motivo']) ? mb_strtoupper(trim($_POST['motivo'])) : null,
            'Observaciones' => isset($_POST['observaciones']) ? mb_strtoupper(trim($_POST['observaciones'])) : null,
            'Estado' => $_POST['estado'] ?? 'Pendiente',
            'id' => $id_reserva
        ];

        if ($datos['Duracion'] === false) {
            throw new Exception("La duración debe ser un número entero mayor o igual a 30");
        }

         // Validar fecha no pasada
         if (new DateTime($datos['Fecha']) < new DateTime('today')) {
            throw new Exception("No se pueden crear reservas en fechas pasadas");
        }

        // Verificar disponibilidad (excluyendo esta reserva)
        $sqlCheck = "SELECT ID FROM reservas 
                    WHERE Fecha = :Fecha 
                    AND Hora = :Hora 
                    AND Juzgado = :Juzgado 
                    AND ID != :id";
        $stmtCheck = $db->prepare($sqlCheck);
        $stmtCheck->execute([
            'Fecha' => $datos['Fecha'],
            'Hora' => $datos['Hora'],
            'Juzgado' => $datos['Juzgado'],
            'id' => $id_reserva
        ]);

        if ($stmtCheck->fetch()) {
            throw new Exception("❌ El juzgado ya tiene una reserva para esa fecha y hora");
        }

        // Después de verificar la disponibilidad básica, añade esta validación:

// Convertir horas a minutos para comparación
$horaInicio = explode(':', $datos['Hora']);
$minutosInicio = $horaInicio[0] * 60 + $horaInicio[1];
$minutosFin = $minutosInicio + $datos['Duracion'];

// Verificar solapamiento con otras reservas en el mismo juzgado y fecha
$sqlSolapamiento = "SELECT ID, Hora, Duracion FROM reservas 
                   WHERE Fecha = :Fecha 
                   AND Juzgado = :Juzgado 
                   AND ID != :id";
$stmtSolapamiento = $db->prepare($sqlSolapamiento);
$stmtSolapamiento->execute([
    'Fecha' => $datos['Fecha'],
    'Juzgado' => $datos['Juzgado'],
    'id' => $id_reserva
]);

while ($otraReserva = $stmtSolapamiento->fetch(PDO::FETCH_OBJ)) {
    $otraHora = explode(':', $otraReserva->Hora);
    $otraMinutosInicio = $otraHora[0] * 60 + $otraHora[1];
    $otraMinutosFin = $otraMinutosInicio + $otraReserva->Duracion;
    
    // Verificar si los intervalos se solapan
    if (($minutosInicio >= $otraMinutosInicio && $minutosInicio < $otraMinutosFin) ||
        ($minutosFin > $otraMinutosInicio && $minutosFin <= $otraMinutosFin) ||
        ($minutosInicio <= $otraMinutosInicio && $minutosFin >= $otraMinutosFin)) {
        
        $horaFormateada = date("g:i a", strtotime($otraReserva->Hora));
        throw new Exception("❌ La reserva se solapa con otra existente a las $horaFormateada (dura $otraReserva->Duracion minutos)");
    }
}


        // Actualización en base de datos
        $sql = "UPDATE reservas SET
                numeroCarpeta = :numeroCarpeta,
                TipoProcedimiento = :TipoProcedimiento,
                Fecha= :Fecha,
                Hora = :Hora,
                Juzgado = :Juzgado,
                Duracion = :Duracion,
                Puesto = :Puesto,
                Motivo = :Motivo,
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
                    <!-- Campo oculto para el ID -->
                    <input type="hidden" name="id" value="<?php echo $reserva->ID; ?>">

                    <!-- Extraer solo el número de carpeta (sin "DEMANDA-") -->
                    <?php 
                    $numero_carpeta = str_replace('EXPEDIENTE-', '', $reserva->numeroCarpeta);
                    ?>

                    <div class="form-group mb-3">
                        <label for="numero_carpeta">Número y Año de Carpeta</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">EXPEDIENTE-</span>
                            </div>
                            <input type="text" class="form-control" id="numero_carpeta" name="numero_carpeta" 
                                value="<?php echo htmlspecialchars($numero_carpeta); ?>" 
                                placeholder="1245-2024" pattern="\d+-\d{4}" required>
                        </div>
                        <small class="form-text text-muted">Formato correcto: 1245-2024</small>
                    </div>

                    <div class="row mb-3">
                    <!-- Primera columna - Campo de fecha -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="fecha">Fecha</label>
                            <input type="date" class="form-control" id="fecha" name="fecha"
                            value="<?php echo htmlspecialchars($reserva->Fecha); ?>" required>
                            <div class="invalid-feedback">Por favor ingrese una fecha válida</div>
                        </div>
                    </div>

                    <!-- Segunda columna - Campo de hora -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="hora">Hora</label>
                            <input type="time" class="form-control" id="hora" name="hora"
                            value="<?php echo htmlspecialchars($reserva->Hora); ?>" step="60" pattern="[0-9]{2}:[0-9]{2}" required>
                            <div class="invalid-feedback">Ingrese una hora válida</div>
                        </div>
                    </div>

                    <!-- Tercera columna - Campo de duración -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="duracion">Duración (minutos)</label>
                            <input type="number" class="form-control" id="duracion" name="duracion"
                            value="<?php echo htmlspecialchars($reserva->Duracion); ?>" 
                                placeholder="Ej: 30" min="30" step="1" required>
                            <div class="invalid-feedback">La duración mínima es 30 minutos</div>
                        </div>
                    </div>
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
                        <label for="puesto">Puesto</label>
                        <input type="text" class="form-control text-uppercase" id="puesto" name="puesto" 
                            value="<?php echo htmlspecialchars($reserva->Puesto); ?>" 
                            placeholder="ANALISTA JR 1" 
                            pattern="[A-Z0-9 ]+" 
                            oninput="this.value = this.value.toUpperCase(); this.value = this.value.replace(/[^A-Z0-9 ]/g, '');"
                            required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="observaciones">Observaciones</label>
                        <textarea class="form-control text-uppercase" id="observaciones" name="observaciones" 
                                rows="2" maxlength="500"
                                oninput="this.value = this.value.toUpperCase()"><?php echo htmlspecialchars($reserva->Observaciones ?? ''); ?></textarea>
                    </div>
                    

                    <div class="form-group mb-3">
                        <label for="estado">Estado</label>
                        <select class="form-control" id="estado" name="estado" required>
                            <option value="Pendiente" style="background-color:rgba(246, 255, 0, 0.38); font-weight: 600;" <?php echo ($reserva->Estado ?? 'Pendiente') == 'Pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="Confirmado" style="background-color:rgba(21, 255, 9, 0.35); font-weight: 600;" <?php echo ($reserva->Estado ?? 'Pendiente') == 'Confirmado' ? 'selected' : ''; ?>>Confirmado</option>
                            <option value="Cancelado" style="background-color:rgba(255, 9, 9, 0.35); font-weight: 600;" <?php echo ($reserva->Estado ?? 'Pendiente') == 'Cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                        </select>
                    </div>

                    <div class="form-group mb-4">
                        <label for="motivo">Motivo</label>
                        <input type="text" class="form-control text-uppercase" id="motivo" name="motivo"
                            placeholder="SOLICITUD DE REVISIÓN"
                            value="<?php echo isset($reserva->Motivo) ? strtoupper($reserva->Motivo) : ''; ?>"
                            oninput="this.value = this.value.toUpperCase()">
                    </div>

                    <div class="form-group mb-4">
                        <label for="juzgado">Juzgado</label>
                        <select class="form-control" id="juzgado" name="juzgado" required>
                            
                            <option value="1 F" <?php echo ($reserva->Juzgado ?? '') == '1 F' ? 'selected' : ''; ?>>1 FAMILIARES</option>
                            <option value="2 F" <?php echo ($reserva->Juzgado ?? '') == '2 F' ? 'selected' : ''; ?>>2 FAMILIARES</option>
                            <option value="3 F" <?php echo ($reserva->Juzgado ?? '') == '3 F' ? 'selected' : ''; ?>>3 FAMILIARES</option>
                        </select>
                    </div>
                    
                    <div class="text-start mt-4">
                        <a href="../registro-app.php" class="btn btn-danger px-4 me-2">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4 me-2">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    // Mostrar mensaje si viene en la URL (para redirecciones con mensajes)
    const urlParams = new URLSearchParams(window.location.search);
    const message = urlParams.get('message');
    const messageType = urlParams.get('messageType');
    
    if (message) {
        alert(decodeURIComponent(message));
    }
    // Solución para Chrome/Edge (elimina segundos al seleccionar)
document.getElementById('hora').addEventListener('change', function() {
        if(this.value.length > 5) {
            this.value = this.value.substring(0, 5); // Corta los segundos
        }
    });


    
// Función para verificar solapamiento
async function verificarSolapamiento() {
    const fecha = document.getElementById('fecha').value;
    const hora = document.getElementById('hora').value;
    const duracion = document.getElementById('duracion').value;
    const juzgado = document.getElementById('juzgado').value;
    const idReserva = <?php echo $id_reserva; ?>;

    if (!fecha || !hora || !duracion || !juzgado) return;

    try {
        const response = await fetch('../api/verificar-solapamiento.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                fecha,
                hora,
                duracion,
                juzgado,
                id_reserva: idReserva
            })
        });

        const data = await response.json();
        
        if (data.solapamiento) {
            alert(`Advertencia: Esta reserva se solapa con:\n${data.mensaje}`);
            document.getElementById('hora').focus();
        }
    } catch (error) {
        console.error('Error al verificar solapamiento:', error);
    }
}

// Event listeners
document.getElementById('hora').addEventListener('change', verificarSolapamiento);
document.getElementById('duracion').addEventListener('change', verificarSolapamiento);
document.getElementById('juzgado').addEventListener('change', verificarSolapamiento);

</script>
    
</body>
</html>