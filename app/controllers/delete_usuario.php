<?php
// Manteniendo tu ruta exacta
require_once '../models/conexion.php';

// Verificar si la conexión se estableció correctamente
if (!isset($db) || !($db instanceof PDO)) {
    header('Content-Type: application/json');
    echo json_encode([
        'error' => true,
        'message' => 'Error: La conexión a la base de datos no está disponible'
    ]);
    exit;
}

header('Content-Type: application/json');

try {
    // 1. Verificar método POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido', 405);
    }

    // 2. Validar ID del usuario
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if (!$id || $id <= 0) {
        throw new Exception('ID de usuario no válido', 400);
    }

    // 3. Verificar si el usuario existe
    $check = $db->prepare("SELECT id FROM usuarios WHERE id = ?");
    $check->execute([$id]);
    
    if (!$check->fetch()) {
        throw new Exception('El usuario no existe', 404);
    }

    // 4. Eliminar usuario
    $stmt = $db->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->execute([$id]);

    // 5. Respuesta exitosa
    echo json_encode([
        'success' => true,
        'message' => 'Usuario eliminado correctamente'
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => 'Error de base de datos, el usuario tiene reservas pendientes: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}
exit;