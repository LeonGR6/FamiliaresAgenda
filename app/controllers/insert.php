<?php
header('Content-Type: application/json'); // Añadir esta línea al inicio

# Verificar si se ha enviado el formulario
if (!isset($_POST['oculto'])) {
    echo json_encode(['success' => false, 'message' => ' ⚠️ Formulario no enviado']);
    exit();
}

# Incluir el archivo de conexión a la base de datos
include '../models/conexion.php';

# Obtener y sanitizar los datos del formulario
$nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
$apellidos = filter_input(INPUT_POST, 'apellidos', FILTER_SANITIZE_STRING);
$correo = filter_input(INPUT_POST, 'correo', FILTER_SANITIZE_EMAIL);
$servicio = filter_input(INPUT_POST, 'servicio', FILTER_SANITIZE_STRING);
$fecha = filter_input(INPUT_POST, 'fecha', FILTER_SANITIZE_STRING);
$hora = filter_input(INPUT_POST, 'hora', FILTER_SANITIZE_STRING);
$mensaje = filter_input(INPUT_POST, 'mensaje', FILTER_SANITIZE_STRING);
$estado = filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_STRING);

# Verificar si ya existe una cita en la misma fecha y hora
$consulta = $db->prepare("SELECT COUNT(*) FROM reservas WHERE fecha = ? AND hora = ?");
$consulta->execute([$fecha, $hora]);
$existeCita = $consulta->fetchColumn();

if ($existeCita > 0) {
    echo json_encode([
        'success' => false, 
        'message' => ' ⚠️ Ya existe una cita programada para la misma fecha y hora.'
    ]);
    exit();
}

# Insertar el nuevo registro si no hay conflicto
$sentencia = $db->prepare("INSERT INTO reservas(nombre, apellidos, correo, servicio, fecha, hora, mensajeadicional, estado)
VALUES(?,?,?,?,?,?,?,?)");

if ($sentencia->execute([$nombre, $apellidos, $correo, $servicio, $fecha, $hora, $mensaje, $estado])) {
    echo json_encode([
        'success' => true,
        'message' => ' ✅ Cita registrada con éxito',
        'data' => [
            'nombre' => $nombre,
            'apellidos' => $apellidos,
            'correo' => $correo,
            'servicio' => $servicio,
            'fecha' => $fecha,
            'hora' => $hora
        ]
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => '❌ Error al insertar datos en la base de datos'
    ]);
}
?>