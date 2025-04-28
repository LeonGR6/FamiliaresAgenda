<?php
require_once '../models/conexion.php';

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'ID no proporcionado']);
    exit();
}

$id = $_GET['id'];

try {
    $sql = "SELECT * FROM reservas WHERE ID = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$id]);
    
    $reserva = $stmt->fetch(PDO::FETCH_OBJ);
    
    if ($reserva) {
        echo json_encode($reserva);
    } else {
        echo json_encode(['error' => 'Reserva no encontrada']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error de base de datos: ' . $e->getMessage()]);
}
?>