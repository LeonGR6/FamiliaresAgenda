<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../views/login.php");
    exit();
}

require_once 'inc/header.php';
require_once 'inc/navbar_app.php';
?>

<body>
    <div class="container py-5">
        <h2 class="text-center mb-5">Bienvenido al Sistema De Reservas.</h2>
        <div class="row justify-content-center g-4">
            <!-- Card Agenda -->
            <div class="col-md-3">
                <a href="agenda.php" class="module-card-link">
                    <div class="card module-card text-center shadow h-100">
                        <div class="card-body d-flex flex-column align-items-center justify-content-center">
                            <div class="module-icon mb-3">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <h5 class="card-title">Agenda</h5>
                            <p class="card-text">Consulta y administra las reservaciones en agenda.</p>
                        </div>
                    </div>
                </a>
            </div>
            <!-- Card Registro -->
            <div class="col-md-3">
                <a href="registro-app.php" class="module-card-link">
                    <div class="card module-card text-center shadow h-100">
                        <div class="card-body d-flex flex-column align-items-center justify-content-center">
                            <div class="module-icon mb-3">
                                <i class="fas fa-edit"></i>
                            </div>
                            <h5 class="card-title">Registro</h5>
                            <p class="card-text">Registra nuevas reservaciones o consulta.</p>
                        </div>
                    </div>
                </a>
            </div>
            <!-- Card Dashboard -->
            <div class="col-md-3">
                <a href="dashboard.php" class="module-card-link">
                    <div class="card module-card text-center shadow h-100">
                        <div class="card-body d-flex flex-column align-items-center justify-content-center">
                            <div class="module-icon mb-3">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h5 class="card-title">Dashboard</h5>
                            <p class="card-text">Visualiza estadísticas y reportes del sistema.</p>
                        </div>
                    </div>
                </a>
            </div>
            
            <!-- Card Usuarios -->
            <?php if ($isAdmin): ?>
    <div class="col-md-3">
        <a href="usuarios.php" class="module-card-link">
            <div class="card module-card text-center shadow h-100">
                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                    <div class="module-icon mb-3">
                        <i class="fas fa-users"></i>
                    </div>
                    <h5 class="card-title">Usuarios</h5>
                    <p class="card-text">Administra los usuarios del sistema.</p>
                </div>
            </div>
        </a>
    </div>
<?php endif; ?>

        </div>
    </div>
    <style>
        .module-card-link {
            text-decoration: none;
        }
        .module-card {
            background: linear-gradient(135deg, #232526 0%, #232526 100%);
            color: #fff;
            border: none;
            border-radius: 1.2rem;
            transition: transform 0.2s, box-shadow 0.2s, background 0.3s;
            box-shadow: 0 4px 24px 0 rgba(200,178,115,0.15);
            cursor: pointer;
        }
        .module-card:hover, .module-card:focus {
            transform: translateY(-8px) scale(1.04);
            box-shadow: 0 8px 32px 0 #C8B27380;
            background: linear-gradient(135deg, #C8B273 0%, #232526 80%);
            color: #232526;
        }
        .module-icon {
            font-size: 3rem;
            color: #C8B273;
            background: #232526;
            border-radius: 50%;
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 12px 0 #C8B27340;
            transition: background 0.3s, color 0.3s;
        }
        .module-card:hover .module-icon {
            background: #fff8dc;
            color: #232526;
        }
        #infooral-img {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
            width: 120px;
            height: auto;
        }
        .module-card-link,
.module-card-link:visited,
.module-card-link:active,
.module-card-link:focus,
.module-card-link:hover {
    text-decoration: none !important;
    color: inherit !important;
    outline: none;
}
    </style>
    <!-- Font Awesome CDN para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <img id="infooral-img" src="/mvc-php/public/images/infooral.png" alt="Info Oral">
</body>
<?php include("inc/footerq.php"); ?>