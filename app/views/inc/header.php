<?php
// Obtener el nombre del archivo actual sin la extensión
$current_page = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);

// Convertir a formato título (ej: "mis-citas" => "Mis Citas")
$page_title = str_replace('-', ' ', $current_page);
$page_title = ucwords($page_title);

// Si es la página principal, puedes personalizarla
if($current_page == 'index') {
    $page_title = 'Inicio';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" href="/mvc-php/favicon.ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/mvc-php/public/css/style.css">
    <title><?php echo $page_title; ?></title>
    <!-- Incluye las bibliotecas de Bootstrap y Datepicker -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" rel="stylesheet">
    <!-- Font Awesome 5 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- Datatables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
<<<<<<< HEAD
=======
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
>>>>>>> ff3078a (Primer commit: Inicialización del proyecto)
    
</head>
