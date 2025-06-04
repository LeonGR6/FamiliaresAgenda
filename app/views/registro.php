<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../views/login.php");
    exit();
}

require_once '../models/conexion.php';

// Verificar si la conexión existe
if (!isset($db)) {
    die("Error: No se pudo establecer conexión a la base de datos");
}

// Obtener el cargo del usuario actual
$query = $db->prepare("SELECT cargo FROM usuarios WHERE id = ?");
$query->execute([$_SESSION['usuario_id']]);
$user = $query->fetch(PDO::FETCH_ASSOC);

// Verificar si el usuario es administrador
if ($user['cargo'] !== 'Administrador') {
    header("Location: inicio.php"); // o la página que corresponda
    exit();
}

require_once 'inc/header.php'; 
require_once 'inc/defnavbar.php'; 
?>

<body>
    <div class="container mt-3 mt-md-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <form id="registroForm" method="POST" action="/mvc-php/app/controllers/registrar_usuario.php">
                    <div id="registroCard" class="card bg-light shadow-lg text-dark mb-5">
                        <div class="card-header text-center bg-dark text-white py-3">
                            <h1 class="h3 mb-0" style="font-size: calc(1.2rem + 0.6vw)">Registrar Usuario</h1>
                        </div>
                        <div class="card-body p-3 p-md-4">
                            <div class="text-center mb-3 mb-md-4">
                                <img src="/mvc-php/public/images/flamacom.png" alt="Logo" class="img-fluid" style="max-width: 100px; height: auto;">
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
                            
                            <div class="text-center mt-3">
                                <a href="/mvc-php/app/views/login.php" class="text-dark">¿Ya tienes una cuenta? Inicia Sesión</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Mensaje de éxito (oculto inicialmente) -->
    <div id="mensajeExito" class="container mt-5" style="display: none;">
        <div class="row">
            <div class="col-sm-12">
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading">¡Formulario de Registro Enviado con Éxito!</h4>
                    <p>Gracias por enviar tu formulario de registro. Hemos recibido tu solicitud.</p>
                    <p>¡Felicidades! 🎉 Tu registro se ha completado con éxito.
                        <br>
                    Ahora puedes explorar todas las funciones y beneficios que tenemos para ti.
                    </p>
                    <hr>
                    <p class="mb-0">Te vamos a redirigir al inicio de sesión para que accedas a tu cuenta
                     Si no ocurre automáticamente, puedes hacer clic en el siguiente enlace para continuar:
                     <a href="/mvc-php/app/views/login.php">Iniciar sesión</a>.
                    <p>Redirigiendo en <span id="countdown">10</span> segundos...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- 1. jQuery (siempre primero) -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- 2. Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <!-- 3. Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    <!-- 4. Tus scripts (registro.js, SweetAlert, etc.) -->
    <script src="/mvc-php/public/js/registro.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Script para autocompletar el campo de usuario -->
    
</body>
</html>