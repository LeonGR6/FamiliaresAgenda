<?php
session_start();
require_once '../models/conexion.php';

header('Content-Type: application/json'); // Establece el tipo de contenido como JSON

$response = ['success' => false, 'message' => ''];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validación de campos vacíos
    if (empty($_POST["username"]) || empty($_POST["password"])) {
        $response['message'] = " ⚠️ Por favor, completa todos los campos.";
        echo json_encode($response);
        exit();
    }

    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    try {
        $query = "SELECT id, nombre, password, cargo FROM usuarios WHERE username = :username LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch();

            if (password_verify($password, $row['password'])) {
                $_SESSION['usuario_id'] = $row['id'];
                $_SESSION['nombre'] = $row['nombre'];
                $_SESSION['cargo'] = $row['cargo']; // Guardar el cargo en la sesión

                $response['success'] = true;
                $response['redirect'] = '../views/inicio.php'; // Ruta a redirigir
            } else {
                $response['message'] = "Usuario o contraseña incorrectos.";
            }
        } else {
            $response['message'] = "Usuario o contraseña incorrectos.";
        }
    } catch (PDOException $e) {
        $response['message'] = "Error en el sistema: " . $e->getMessage();
    }
} else {
    $response['message'] = " Método no permitido.";
}

echo json_encode($response);
exit();
?>