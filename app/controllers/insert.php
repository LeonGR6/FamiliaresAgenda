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
            
            'duracion' => 'Duración',
        ];
        
        foreach ($requiredFields as $field => $name) {
            if (empty($_POST[$field])) {
                throw new Exception("El campo $name es obligatorio");
            }
        }

        // Procesar datos
        $numeroCarpeta = 'EXPEDIENTE-' . htmlspecialchars($_POST['numero_carpeta']);
        $tipoProcedimiento = strtoupper(htmlspecialchars($_POST['tipo']));
        $fecha = htmlspecialchars($_POST['fecha']);
        $hora = htmlspecialchars($_POST['hora']);
        $juzgado = strtoupper(htmlspecialchars($_POST['juzgado']));
        $duracion = !empty($_POST['duracion']) ? (int)$_POST['duracion'] : null;
        $puesto = !empty($_POST['puesto']) ? strtoupper(htmlspecialchars($_POST['puesto'])) : null;
        $motivo = !empty($_POST['motivo']) ? strtoupper(htmlspecialchars($_POST['motivo'])) : null;
        $observaciones = !empty($_POST['observaciones']) ? strtoupper(htmlspecialchars($_POST['observaciones'])) : null;

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
            
            // Verificar si los intervalos se solapan
            if (($minutosInicio >= $otraMinutosInicio && $minutosInicio < $otraMinutosFin) ||
                ($minutosFin > $otraMinutosInicio && $minutosFin <= $otraMinutosFin) ||
                ($minutosInicio <= $otraMinutosInicio && $minutosFin >= $otraMinutosFin)) {
                
                $horaFormateada = date("g:i a", strtotime($otraReserva->Hora));
                throw new Exception("❌ La reserva se solapa con otra existente a las $horaFormateada (dura $otraReserva->Duracion minutos)");
            }
        }

        // Insertar en DB
        $sql = "INSERT INTO reservas (
                    numeroCarpeta, TipoProcedimiento, Fecha, Hora, Juzgado, Duracion, 
                    Puesto, Motivo, Observaciones, Estado, usuario_id
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

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
            $_SESSION['usuario_id']
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