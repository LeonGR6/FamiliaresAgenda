<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Debes iniciar sesión para realizar reservas'
    ]);
    exit();
}

require_once '../models/conexion.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['oculto'])) {
    try {
        // Validación básica
        $requiredFields = [
            'numero_carpeta' => 'Número de carpeta',
            'tipo' => 'Tipo de procedimiento',
            'fecha' => 'Fecha',
            'hora' => 'Hora',
            'juzgado' => 'Juzgado',
            'sala' => 'Tipo de espacio (Sala/Cámara)',
            'duracion' => 'Duración'
        ];
        
        foreach ($requiredFields as $field => $name) {
            if (empty($_POST[$field])) {
                throw new Exception("El campo $name es obligatorio");
            }
        }

        // Procesar datos
        $numeroCarpeta = htmlspecialchars($_POST['numero_carpeta']);
        $tipoProcedimiento = strtoupper(htmlspecialchars($_POST['tipo']));
        $fecha = htmlspecialchars($_POST['fecha']);
        $hora = htmlspecialchars($_POST['hora']);
        $juzgado = strtoupper(htmlspecialchars($_POST['juzgado']));
        $tipoEspacio = strtoupper(htmlspecialchars($_POST['sala'])); // "SALA" o "CAMARA "
        $duracion = (int)$_POST['duracion'];
        $cargo = isset($_SESSION['cargo']) ? strtoupper(htmlspecialchars($_SESSION['cargo'])) : (!empty($_POST['cargo']) ? strtoupper(htmlspecialchars($_POST['cargo'])) : null);
        $nombrePersona = isset($_SESSION['nombre']) ? strtoupper(htmlspecialchars($_SESSION['nombre'])) : null;
        $observaciones = !empty($_POST['observaciones']) ? strtoupper(htmlspecialchars($_POST['observaciones'])) : null;

        // Validar tipo de espacio
        if ($tipoEspacio !== 'SALA' && $tipoEspacio !== 'CAMARA') {
            throw new Exception("❌ Tipo de espacio inválido. Elige 'Sala' o 'Cámara Gesell'.");
        }

        if (!DateTime::createFromFormat('Y-m-d', $fecha)) {
            throw new Exception("Formato de fecha inválido");
        }
    
        // Validar duración
        if (!is_numeric($duracion) || $duracion <= 0 || $duracion > 480) {
            throw new Exception("Duración inválida. Debe ser entre 1 y 480 minutos");
        }

         // Validar fecha no pasada
         if (new DateTime($fecha) < new DateTime('today')) {
            throw new Exception("No se pueden crear reservas en fechas anteriores a el dia de hoy");
        }

        // Definir espacios disponibles según lo seleccionado
        if ($tipoEspacio === 'SALA') {
            $espaciosDisponibles = ['SALA 1', 'SALA 2', 'SALA 3'];
        } else {
            $espaciosDisponibles = ['CAMARA 1', 'CAMARA 2'];
        }

        $espacioAsignado = null;

        // Verificar disponibilidad en cada espacio
        foreach ($espaciosDisponibles as $espacio) {
            $sqlSolapamiento = "SELECT COUNT(*) as count FROM reservas 
                                WHERE Fecha = ? 
                                AND Sala = ?
                                AND (
                                /* Reservas existentes que solapan con el inicio de la nueva reserva (incluyendo margen) */
                                /* Check if the new reservation overlaps with the start of an existing reservation (including 10-minute margin) */
                                (Hora <= ? AND ADDTIME(Hora, SEC_TO_TIME((Duracion + 10) * 60)) > ?)
                                OR 
                                /* Check if the new reservation overlaps with the end of an existing reservation (including 10-minute margin) */
                                (Hora < ADDTIME(?, SEC_TO_TIME((? + 10) * 60)) 
                                 AND ADDTIME(Hora, SEC_TO_TIME(Duracion * 60)) >= ADDTIME(?, SEC_TO_TIME(? * 60)))
                                )";
            

            $horaFin = date('H:i:s', strtotime("+{$duracion} minutes", strtotime($hora)));

            $stmtSolapamiento = $db->prepare($sqlSolapamiento);
            $stmtSolapamiento->execute([
                $fecha,
                $espacio,
                $hora, $hora,
                $hora,$duracion, $hora, $duracion
            ]);

            $result = $stmtSolapamiento->fetch(PDO::FETCH_OBJ);

            if ($result->count == 0) {
                $espacioAsignado = $espacio;
                break;
            }
        }

        if (!$espacioAsignado) {
            $horaFinConMargen = date('H:i', strtotime("+".($duracion+10)." minutes", strtotime($hora)));
            $tipoMostrar = ($tipoEspacio === 'SALA') ? 'salas' : 'cámaras Gesell';
            
            throw new Exception("No hay {$tipoMostrar} disponibles para el horario solicitado (incluyendo 10 minutos para desocupar el espacio)."
            
            );
        }

        // Insertar la reserva
        $sql = "INSERT INTO reservas (
                numeroCarpeta, TipoProcedimiento, Fecha, Hora, Juzgado, Duracion, 
                Nombre, Cargo, Observaciones, Estado, usuario_id, Sala
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $db->prepare($sql);
        $result = $stmt->execute([
            $numeroCarpeta,
            $tipoProcedimiento,
            $fecha,
            $hora,
            $juzgado,
            $duracion,
            $nombrePersona,
            $cargo,
            $observaciones,
            'Pendiente',
            $_SESSION['usuario_id'],
            $espacioAsignado
        ]);

        if ($result) {
            $response = [
                'success' => true,
                'message' => '✅ Reserva registrada exitosamente en ' . $espacioAsignado
            ];
        } else {
            throw new Exception("❌ Error al guardar en la base de datos");
        }

    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
        $response['message'] = $e->getMessage();
    }
} else {
    http_response_code(405); // Set HTTP status code to 405 Method Not Allowed
    $response['message'] = 'Método no permitido';
}
// Encode the response in JSON format
echo json_encode($response);
