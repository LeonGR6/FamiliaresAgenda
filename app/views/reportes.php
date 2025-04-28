<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../views/login.php");
    exit();
}

?>
<?php require_once 'inc/header.php'; ?>
<?php require_once 'inc/navbar_app.php'; ?>

<body>

    <h1>Reportes</h1>
    
</body>

