<?php
header('Content-Type: application/json'); // Importante para respuestas JSON

session_start();
require_once '../../config/database.php'; // Ajusta la ruta según tu estructura

// Verificar si es una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => '❌ Método no permitido']);
    exit;
}

// Verificar permisos del usuario (si es necesario)
if (!isset($_SESSION['usuario_id']) || $_SESSION['cargo'] !== 'Administrador') {
    echo json_encode(['success' => false, 'message' => '❌ No tienes permisos para esta acción']);
    exit;
}

// Obtener y sanitizar datos
$nombre = trim($_POST['nombre'] ?? '');
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$cargo = $_POST['cargo'] ?? '';

// Validaciones básicas
$errors = [];

if (empty($nombre)) $errors[] = 'El nombre es requerido';
if (empty($username)) $errors[] = 'El usuario es requerido';
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email inválido';
if (empty($password) || strlen($password) < 8) $errors[] = 'La contraseña debe tener al menos 8 caracteres';
if (empty($cargo) || !in_array($cargo, ['Espectador', 'Personal', 'Administrador'])) {
    $errors[] = 'Cargo inválido';
}

// Si hay errores, devolverlos
if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => '❌ ' . implode(', ', $errors)]);
    exit;
}

try {
    // Hash de la contraseña
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    
    // Insertar en la base de datos (ejemplo con PDO)
    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, username, email, password, cargo) 
                          VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$nombre, $username, $email, $passwordHash, $cargo]);
    
    // Éxito
    echo json_encode([
        'success' => true, 
        'message' => '✅ Usuario registrado con éxito!'
    ]);
    
} catch (PDOException $e) {
    // Manejar errores de base de datos
    $errorMessage = '❌ Error al registrar usuario: ';
    
    // Mensaje más amigable para errores comunes
    if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
        if (strpos($e->getMessage(), 'username')) {
            $errorMessage .= 'El nombre de usuario ya existe';
        } else if (strpos($e->getMessage(), 'email')) {
            $errorMessage .= 'El email ya está registrado';
        } else {
            $errorMessage .= 'Datos duplicados';
        }
    } else {
        $errorMessage .= 'Error en la base de datos';
    }
    
    echo json_encode(['success' => false, 'message' => $errorMessage]);
}