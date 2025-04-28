<?php
require_once '../models/conexion.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true) ?: $_POST;

if (!isset($data['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
    exit();
}

try {
    $sql = "UPDATE reservas SET 
            Nombre = ?, 
            Apellidos = ?, 
            Correo = ?, 
            Servicio = ?, 
            Fecha = ?, 
            Hora = ?, 
            MensajeAdicional = ?, 
            Estado = ? 
            WHERE ID = ?";
    
    $stmt = $db->prepare($sql);
    $success = $stmt->execute([
        $data['nombre'],
        $data['apellidos'],
        $data['correo'],
        $data['servicio'],
        $data['fecha'],
        $data['hora'],
        $data['mensaje'] ?? null,
        $data['estado'],
        $data['id']
    ]);
    
    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Reserva actualizada correctamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar la reserva']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()]);
}
?>