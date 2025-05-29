<?php
session_start();

// Verificación de autenticación
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../views/login.php");
    exit();
}

require_once '../models/conexion.php';

// 1. Validación del ID de reserva
$id_reserva = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id_reserva) {
    $_SESSION['mensaje'] = "ID de reserva inválido o no proporcionado";
    $_SESSION['mensaje_tipo'] = "danger";
    header("Location: ../views/registro-app.php");
    exit();
}

// 2. Obtener datos de la reserva
try {
    $sentencia = $db->prepare("SELECT * FROM reservas WHERE ID = ?");
    $sentencia->execute([$id_reserva]);
    $reserva = $sentencia->fetch(PDO::FETCH_OBJ);

    if (!$reserva) {
        $_SESSION['mensaje'] = "Reserva no encontrada";
        $_SESSION['mensaje_tipo'] = "danger";
        header("Location: ../views/registro-app.php");
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['mensaje'] = "Error al obtener reserva: " . $e->getMessage();
    $_SESSION['mensaje_tipo'] = "danger";
    header("Location: ../views/registro-app.php");
    exit();
}

// 3. Procesamiento del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validación de campos requeridos
        $campos_requeridos = [
            'numero_carpeta' => "Número de carpeta",
            'fecha' => "Fecha",
            'hora' => "Hora",
            'tipo' => "Tipo de procedimiento",
            
        ];
        
        foreach ($campos_requeridos as $campo => $nombre) {
            if (empty($_POST[$campo])) {
                throw new Exception("El campo {$nombre} es requerido");
            }
        }

        // Sanitización y formato de datos
        $datos = [
            'numeroCarpeta' => 'DEMANDA-' . preg_replace('/[^0-9-]/', '', $_POST['numero_carpeta']),
            'TipoProcedimiento' => mb_strtoupper(trim($_POST['tipo'])),
            'Fecha' => $_POST['fecha'],
            'Duracion' => filter_var($_POST['duracion'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 30]]),
            'Hora' => $_POST['hora'],
            'Observaciones' => isset($_POST['observaciones']) ? mb_strtoupper(trim($_POST['observaciones'])) : null,
            'Estado' => $_POST['estado'] ?? 'PENDIENTE',
            'id' => $id_reserva
        ];

        if ($datos['Duracion'] === false) {
            throw new Exception("La duración debe ser un número entero mayor o igual a 30");
        }

        // Actualización en base de datos
        $sql = "UPDATE reservas SET
                numeroCarpeta = :numeroCarpeta,
                TipoProcedimiento = :TipoProcedimiento,
                FechaHora = :FechaHora,
                Duracion = :Duracion,
                Observaciones = :Observaciones,
                Estado = :Estado
                WHERE ID = :id";

        $sentencia = $db->prepare($sql);
        $resultado = $sentencia->execute($datos);

        if ($resultado && $sentencia->rowCount() > 0) {
            $_SESSION['mensaje'] = "Reserva actualizada correctamente";
            $_SESSION['mensaje_tipo'] = "success";
            header("Location: ../views/registro-app.php");
            exit();
        } else {
            throw new Exception("No se realizaron cambios en la reserva");
        }
    } catch (Exception $e) {
        // Mantener los datos para repoblar el formulario
        $reserva = (object) array_merge((array) $reserva, $_POST);
        $_SESSION['mensaje'] = $e->getMessage();
        $_SESSION['mensaje_tipo'] = "danger";
    }
}

// 4. Cargar la vista
require_once '../modals/editar_reserva.php';