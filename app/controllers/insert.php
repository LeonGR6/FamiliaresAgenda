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
            'sala' => 'Sala',
            'duracion' => 'Duración',
            'sala' => 'Tipo de espacio (Sala/Cámara)',
        ];
        
        foreach ($requiredFields as $field => $name) {
            if (empty($_POST[$field])) {
                throw new Exception("El campo $name es obligatorio");
            }
        }

        // Procesar datos
        $numeroCarpeta = 'EXP' . htmlspecialchars($_POST['numero_carpeta']);
        $tipoProcedimiento = strtoupper(htmlspecialchars($_POST['tipo']));
        $fecha = htmlspecialchars($_POST['fecha']);
        $hora = htmlspecialchars($_POST['hora']);
        $juzgado = strtoupper(htmlspecialchars($_POST['juzgado']));
        $sala = strtoupper(htmlspecialchars($_POST['sala']));
        $duracion = !empty($_POST['duracion']) ? (int)$_POST['duracion'] : null;
        $puesto = !empty($_POST['puesto']) ? strtoupper(htmlspecialchars($_POST['puesto'])) : null;
        $motivo = !empty($_POST['motivo']) ? strtoupper(htmlspecialchars($_POST['motivo'])) : null;
        $observaciones = !empty($_POST['observaciones']) ? strtoupper(htmlspecialchars($_POST['observaciones'])) : null;

        // Validar tipo de espacio
        if ($sala !== 'SALA' && $sala !== 'CAMARA') {
            throw new Exception("❌ Tipo de espacio inválido. Elige 'Sala' o 'Camara'.");
        }

        // Asignar SALA o CÁMARA aleatoriamente (se guardará en el campo `sala` de la BD)
        if ($sala === 'SALA') {
            $sala = 'SALA ' . rand(1, 3); // Sala 1, 2 o 3 (random)
        } else {
            $sala = 'CAMARA ' . rand(1, 2); // Cámara 1 o 2 (random)
        }

        if (!DateTime::createFromFormat('Y-m-d', $fecha)) {
            throw new Exception("Formato de fecha inválido");
        }
    
        // Validar duración
        if ($duracion <= 0 || $duracion > 480) {
            throw new Exception("Duración inválida. Debe ser entre 1 y 480 minutos");
        }
    
        // Convertir horas a minutos para comparación
        $horaInicio = explode(':', $hora);
        $minutosInicio = $horaInicio[0] * 60 + $horaInicio[1];
        $minutosFin = $minutosInicio + $duracion;

        // Verificar solapamiento con otras reservas en el mismo juzgado y fecha
        $sqlSolapamiento = "SELECT Hora, Duracion FROM reservas 
                          WHERE Fecha = ?
                          
                          AND Juzgado = ?";
        $stmtSolapamiento = $db->prepare($sqlSolapamiento);
        $stmtSolapamiento->execute([$fecha, $juzgado]);

        while ($otraReserva = $stmtSolapamiento->fetch(PDO::FETCH_OBJ)) {
            $otraHora = explode(':', $otraReserva->Hora);
            $otraMinutosInicio = $otraHora[0] * 60 + $otraHora[1];
            $otraMinutosFin = $otraMinutosInicio + $otraReserva->Duracion;
            
            $otraMinutosFinConMargen = $otraMinutosFin + 30;
            
            // Verificar si los intervalos se solapan (incluyendo el margen de 30 minutos)
            if (($minutosInicio >= $otraMinutosInicio && $minutosInicio < $otraMinutosFinConMargen) ||
                ($minutosFin > $otraMinutosInicio && $minutosFin <= $otraMinutosFinConMargen) ||
                ($minutosInicio <= $otraMinutosInicio && $minutosFin >= $otraMinutosFinConMargen)) {
                
                $horaFormateada = date("g:i a", strtotime($otraReserva->Hora));
                $duracionConMargen = $otraReserva->Duracion + 30;
                throw new Exception("❌ La reserva se solapa con otra existente a las $horaFormateada (dura $otraReserva->Duracion minutos + 30 min de margen)");
            }
        }

        // Insertar en DB
        $sql = "INSERT INTO reservas (
                    numeroCarpeta, TipoProcedimiento, Fecha, Hora, Juzgado, Duracion, 
                    Puesto, Motivo, Observaciones, Estado, usuario_id, sala
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $db->prepare($sql);
        $result = $stmt->execute([
            $numeroCarpeta,
            $tipoProcedimiento,
            $fecha,
            $hora,
            $juzgado,
            $duracion,
            $puesto,
            $motivo,
            $observaciones,
            'Pendiente',
            $_SESSION['usuario_id'],
            $sala
        ]);

        if ($result) {
            $response = [
                'success' => true,
                'message' => '✅ Reserva registrada exitosamente'
            ];
        } else {
            throw new Exception("❌ Error al guardar en la base de datos");
        }

    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }
} else {
    $response['message'] = 'Método no permitido';
}



echo json_encode($response);