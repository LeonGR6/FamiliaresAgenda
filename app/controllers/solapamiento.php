<?php
session_start();
header('Content-Type: application/json');
require_once '../models/conexion.php';

$response = ['solapamiento' => false, 'message' => ''];

try {
    $fecha = $_POST['fecha'] ?? null;
    $hora = $_POST['hora'] ?? null;
    $duracion = (int)($_POST['duracion'] ?? 0);
    $juzgado = $_POST['juzgado'] ?? null;

    // Validaciones básicas
    if (!$fecha || !$hora || !$duracion || !$juzgado) {
        throw new Exception("Datos incompletos para verificación");
    }

    // Convertir horas a minutos
    $horaInicio = explode(':', $hora);
    $minutosInicio = $horaInicio[0] * 60 + $horaInicio[1];
    $minutosFin = $minutosInicio + $duracion;

    // Verificar solapamiento
    $sql = "SELECT Hora, Duracion FROM reservas 
            WHERE Fecha = ? AND Juzgado = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$fecha, $juzgado]);

    while ($reserva = $stmt->fetch(PDO::FETCH_OBJ)) {
        $otraHora = explode(':', $reserva->Hora);
        $otraMinutosInicio = $otraHora[0] * 60 + $otraHora[1];
        $otraMinutosFin = $otraMinutosInicio + $reserva->Duracion;
        
        if (($minutosInicio >= $otraMinutosInicio && $minutosInicio < $otraMinutosFin) ||
            ($minutosFin > $otraMinutosInicio && $minutosFin <= $otraMinutosFin) ||
            ($minutosInicio <= $otraMinutosInicio && $minutosFin >= $otraMinutosFin)) {
            
            $horaFormateada = date("g:i a", strtotime($reserva->Hora));
            $response = [
                'solapamiento' => true,
                'message' => "❌ La reserva se solapa con otra existente a las $horaFormateada (dura $reserva->Duracion minutos)"
            ];
            break;
        }
    }
} catch (Exception $e) {
    $response = ['solapamiento' => false, 'message' => $e->getMessage()];
}

echo json_encode($response);