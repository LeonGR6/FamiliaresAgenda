<nav class="navbar navbar-expand-md bg-dark navbar-dark py-3">
    <div class="container-fluid">
        <!-- Logo -->
        <a class="navbar-brand" href="./">
            <img src="/SIADO-PJAGS/public/images/siaf3.png" 
                 alt="Logo" 
                 style="max-height: 45px;">
        </a>

        <!-- Botón hamburguesa -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" 
                data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" 
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Contenido colapsable -->
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <ul class="navbar-nav me-auto mb-2 mb-md-0">
                <li class="nav-item">
                    <a class="nav-link mx-2" href="inicio.php" id="nav-inicio">
                        <i class="fas fa-home me-1"></i> Inicio
                    </a>
                </li>
                
                <!-- Agenda -->
                <li class="nav-item">
                    <a class="nav-link mx-2" href="agenda.php" id="nav-agenda">
                        <i class="fas fa-calendar-alt me-1"></i> Agenda
                    </a>
                </li>

                <!-- Formulario/Registro -->
                <li class="nav-item">
                    <a class="nav-link mx-2" href="registro-app.php" id="nav-registro">
                        <i class="fas fa-file-alt me-1"></i> Registro
                    </a>
                </li>
                
                <!-- Lista de Contactos 
                 <li class="nav-item">
                    <a class="nav-link mx-2" href="contactos-app.php" id="nav-contactos-lista">
                        <i class="fas fa-address-book me-1"></i> Contactos
                    </a>
                </li> -->       
<?php
// Verificar si la variable ya está definida, si no, obtenerla
if (!isset($isAdmin)) {
    require_once '../models/conexion.php';
    $query = $db->prepare("SELECT cargo FROM usuarios WHERE id = ?");
    $query->execute([$_SESSION['usuario_id']]);
    $user = $query->fetch(PDO::FETCH_ASSOC);
    $isAdmin = ($user['cargo'] === 'Administrador');
}
?>

<li class="nav-item">
    <?php if ($isAdmin): ?>
        <a class="nav-link mx-2" href="usuarios.php" id="nav-usuarios">
            <i class="fas fa-user-cog me-1"></i> Usuarios
        </a>
    <?php endif; ?>
</li>


                 
                 <li class="nav-item">
                    <a class="nav-link mx-2" href="bitacora.php" id="nav-bitacora">
                        <i class="fas fa-chart-bar me-1"></i> Bitácora
                    </a>
                </li>             

                <!-- Notificaciones 
                <li class="nav-item">
                    <a class="nav-link mx-2 position-relative" href="notificar.php" id="nav-notificaciones">
                        <i class="fas fa-bell me-1"></i> Notificaciones
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            0
                        </span>
                    </a>
                </li>             -->


            </ul>
            
            <!-- Botón de logout -->
            <div class="d-flex align-items-center" style="margin-left: auto;">
                <form action="../controllers/logout_usuario.php" method="POST">
                    <input type="hidden" name="action" value="logout">
                    <button class="btn btn-outline-light fw-bold" type="submit">
                        <i class="fas fa-sign-out-alt me-1"></i> Cerrar Sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

<!-- Bootstrap 5 JS Bundle con Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Font Awesome para iconos -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="/SIADO-PJAGS/public/css/navbar_app.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<script src="/SIADO-PJAGS/public/js/navbar_app.js"></script>

