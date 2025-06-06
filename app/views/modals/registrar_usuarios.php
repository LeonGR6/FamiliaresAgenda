<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../views/login.php");
    exit();
}

require_once '../inc/header.php'; 
require_once '../inc/navbar_default.php';
?>

<body>
    <div class="container mt-3 mt-md-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <form id="registroForm" method="POST" action="/SIADO-PJAGS/app/controllers/registrar_usuario">
                    <div id="registroCard" class="card bg-light shadow-lg text-dark mb-5">
                        <div class="card-header text-center bg-dark text-white py-3">
                            <h1 class="h3 mb-0" style="font-size: calc(1.2rem + 0.6vw)">Registrar Usuario</h1>
                        </div>
                        <div class="card-body p-3 p-md-4">
                            <div class="text-center mb-3 mb-md-4">
                                <img src="/SIADO-PJAGS/public/images/flamacom.png" alt="Logo" class="img-fluid" style="max-width: 100px; height: auto;">
                            </div>
                            
                            <div class="form-group mb-3">
                                <label for="nombre" class="form-label">Nombre Completo:</label>
                                <input type="text" id="nombre" class="form-control border-primary rounded-pill w-100" name="nombre" required>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label for="username" class="form-label">Usuario:</label>
                                <input type="text" id="username" class="form-control border-primary rounded-pill w-100" autocomplete="username" name="username" required>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label for="email" class="form-label">Email:</label>
                                <input type="email" id="email" class="form-control border-primary rounded-pill w-100" name="email" autocomplete="username" required>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label for="password" class="form-label">Contraseña:</label>
                                <input type="password" id="password" class="form-control border-primary rounded-pill w-100" autocomplete="current-password" name="password" required>
                            </div>
                            
                            <div class="form-group mb-4 mb-md-5">
                                <label for="cargo" class="form-label">Cargo:</label>
                                <select id="cargo" name="cargo" required class="form-control border-primary rounded-pill w-100">
                                    <option value="" disabled selected>Seleccione un cargo</option>
                                    <option value="Espectador">Espectador</option>
                                    <option value="Personal">Personal</option>
                                    <option value="Administrador">Administrador</option>
                                </select>
                            </div>
                            
                            <div class="row justify-content-center mb-3">
                                <div class="col-12 col-md-8 col-lg-6">
                                    <button type="submit" class="btn sanclear btn-block fw-bold rounded-pill py-2">Registrar</button>
                                </div>
                            </div>



<script src="/SIADO-PJAGS/public/js/registrar_usuarios.js"></script>