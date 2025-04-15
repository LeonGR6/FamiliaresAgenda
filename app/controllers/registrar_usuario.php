<?php
require_once '../models/conexion.php';

if (!isset($db)) {
    echo json_encode([
        "success" => false,
        "message" => "Error de conexión a la base de datos"
    ]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitizar y obtener datos
    $nombre = trim(htmlspecialchars($_POST['nombre'] ?? ''));
    $username = trim(htmlspecialchars($_POST['username'] ?? ''));
    $password = $_POST['password'] ?? '';
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $cargo = $_POST['cargo'] ?? ''; // Valor del <select>

    // Validar campos vacíos
    $camposVacios = [];
    if (empty($nombre)) $camposVacios[] = "Nombre completo";
    if (empty($username)) $camposVacios[] = "Usuario";
    if (empty($password)) $camposVacios[] = "Contraseña";
    if (empty($email)) $camposVacios[] = "Email";
    if (empty($cargo)) $camposVacios[] = "Cargo";

    if (!empty($camposVacios)) {
        echo json_encode([
            "success" => false,
            "message" => "Los siguientes campos son obligatorios: " . implode(", ", $camposVacios)
        ]);
        exit;
    }

    // Validar roles permitidos
    $rolesPermitidos = ['Espectador', 'Personal', 'Administrador'];
    if (!in_array($cargo, $rolesPermitidos)) {
        echo json_encode([
            "success" => false,
            "message" => "El cargo seleccionado no es válido"
        ]);
        exit;
    }

    // Validar formato del username (ejemplo: solo letras minúsculas)
    if (!preg_match('/^[a-z]{4,20}$/', $username)) {
        echo json_encode([
            "success" => false,
            "message" => "El usuario debe contener solo letras minúsculas (4-20 caracteres)"
            "message" => "El usuario debe contener solo letras minúsculas (sin caracteres especiales) y tener entre 4 y 20 caracteres"

        ]);
        exit;
    }

    // Validar email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            "success" => false,
            "message" => "El email no es válido"
        ]);
        exit;
    }

    // Validar contraseña fuerte
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
        echo json_encode([
            "success" => false,
            "message" => "La contraseña debe tener 8+ caracteres, mayúsculas, minúsculas, números y símbolos"
        ]);
        exit;
    }

    try {
        // Verificar si el usuario/email ya existe
        $checkUser = $db->prepare("SELECT id FROM usuarios WHERE username = :username OR email = :email");
        $checkUser->execute([':username' => $username, ':email' => $email]);

        if ($checkUser->rowCount() > 0) {
            echo json_encode([
                "success" => false,
                "message" => "El usuario o email ya están registrados"
            ]);
            exit;
        }

        // Hash de la contraseña
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insertar usuario
        $query = "INSERT INTO usuarios (nombre, username, password, email, cargo) 
                 VALUES (:nombre, :username, :password, :email, :cargo)";
        $stmt = $db->prepare($query);
        
        $success = $stmt->execute([




            ':nombre' => $nombre,
            ':username' => $username,
            ':password' => $hashed_password,
            ':email' => $email,
            ':cargo' => $cargo
        ]);





        if ($success) {
            echo json_encode([
                "success" => true,
                "message" => "¡Registro exitoso! Bienvenido/a"
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "Error al guardar el usuario"
            ]);
        }
    } catch (PDOException $e) {
        error_log("Error en registro: " . $e->getMessage() . " en " . $e->getFile() . " línea " . $e->getLine());
        echo json_encode([
            "success" => false,
            "message" => "Error interno. Por favor, inténtalo más tarde."
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Método no permitido"
    ]);
}
?>