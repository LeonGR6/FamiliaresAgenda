<?php
require_once '../models/conexion.php';

header('Content-Type: application/json');

try {
    // 1. Verificar método POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido', 405);
    }

    // 2. Validar datos
    $required = ['id', 'nombre', 'username', 'email', 'cargo'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("El campo $field es requerido", 400);
        }
    }

    $id = (int)$_POST['id'];
    $nombre = trim($_POST['nombre']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $cargo = trim($_POST['cargo']);

    // 3. Validaciones adicionales
    if (strlen($username) < 3) {
        throw new Exception("El usuario debe tener al menos 3 caracteres", 400);
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Email no válido", 400);
    }

    if (!in_array($cargo, ['Administrador', 'Personal', 'Espectador'])) {
        throw new Exception("Cargo no válido", 400);
    }

    // 4. Actualizar en BD
    $stmt = $db->prepare("UPDATE usuarios SET 
        nombre = ?, 
        username = ?, 
        email = ?, 
        cargo = ? 
        WHERE id = ?");
    
    $success = $stmt->execute([$nombre, $username, $email, $cargo, $id]);

    if (!$success) {
        throw new Exception("Error al actualizar en base de datos");
    }

    echo json_encode([
        'success' => 'Usuario actualizado correctamente'
    ]);

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'error' => $e->getMessage()
    ]);
    exit;
}