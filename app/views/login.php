<?php require_once 'inc/header.php'; ?>
<?php require_once 'inc/defnavbar.php'; ?>

<body class="d-flex flex-column min-vh-50">
    <div class="container my-auto py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                <form id="registroForm" method="POST" action="/SIADO-PJAGS/app/controllers/login_usuario">
                    <div id="registroCard" class="card bg-light shadow-lg text-dark">
                        <div class="card-header text-center bg-dark text-white py-3">
                            <h1 class="h4 mb-0">Iniciar Sesión</h1>
                        </div>
                        <div class="card-body p-4 p-md-5">

                            <?php if(isset($_SESSION['error_message'])): ?>
                                <div class="alert alert-danger">
                                    <?php 
                                    echo $_SESSION['error_message']; 
                                    unset($_SESSION['error_message']);
                                    ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="text-center mb-4">
                                <img src="/SIADO-PJAGS/public/images/flamacom.png" alt="Logo" class="img-fluid" style="max-width: 100px; height: auto;">
                            </div>
                            
                            <div class="form-group mb-3">
                                <label for="username" class="form-label">Usuario:</label>
                                <input type="text" id="username" class="form-control rounded-pill border-primary" name="username" autocomplete="username" required>
                            </div>
                            
                            <div class="form-group mb-4">
                                <label for="password" class="form-label">Contraseña:</label>
                                <input type="password" id="password" class="form-control rounded-pill border-primary" name="password" autocomplete="current-password" required>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn sanclear btn-block fw-bold rounded-pill py-2">Iniciar Sesión</button>
                            </div>
                            <div class="text-center mt-3">
                                <a href="contactanos.php" class="text-dark">¿No tienes una cuenta? Contacta al administrador para regístrate</a>
                            </div>
                            

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/SIADO-PJAGS/public/js/login.js"></script>
</body>
</html>