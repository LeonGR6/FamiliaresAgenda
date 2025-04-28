<?php
header('Content-Type: application/json');

// Incluye el archivo de conexión (que ya tienes y NO quieres modificar)
require_once '../models/conexion.php'; 

// Verifica si se recibió el ID
if (!isset($_POST['id'])) {
    echo json_encode(["success" => false, "message" => " ⚠️ ID no recibido"]);
    exit();
}

$id = $_POST['id'];

try {
    // Usa directamente la variable $db que ya existe en conexion.php
    $query = "DELETE FROM Reservas WHERE ID = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$id]);
    
    // Verifica si se afectó alguna fila
    if ($stmt->rowCount() > 0) {
        echo json_encode(["success" => true, "message" => " ✅ Reserva eliminada"]);
    } else {
        echo json_encode(["success" => false, "message" => " ❌ No se encontró la reserva"]);
    }
} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => " ❌ Error en la base de datos",
        "debug" => $e->getMessage()
    ]);
}
?>