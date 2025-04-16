<?php require_once 'inc/header.php'; ?>
<?php require_once 'inc/defnavbar.php'; ?>

<body>
                                <div class="container mt-5">
                                    <div class="row justify-content-center">
                                        <div class="col-md-6">

                                            
                                                    
                                        <form id="registroForm" method="POST" action="/mvc-php/app/controllers/registrar_usuario.php">
                                        <div id="registroCard" class="card bg-light shadow-lg text-dark mb-5">
                                <div class="card-header text-center bg-dark text-white" style="font-size: 2rem; ">
                                    Iniciar Sesión
                                </div>
                                <div class="card-body p-4">
                                    <div class="text-center mb-3">
                                        <img src="/mvc-php/public/images/flamacom.png" alt="Logo" class="img-fluid" style="width: 100px; height: 120px;">
                                    </div>
                                <div class="card-body p-4">
                                    <div class="form-group mb-3">
                                        <label for="username" class="form-label">Usuario:</label>
                                        <input type="text" id="username" class="form-control rounded border-primary rounded-pill" style="width: 420px;" name="username" required>
                                    </div>
                                    <div class="form-group mb-5">
                                        <label for="password" class="form-label">Contraseña:</label>
                                        <input type="password" id="password" class="form-control border-primary rounded-pill" style="width: 420px;" name="password" required>
                                    </div>
                                    
                                    <div class="row justify-content-center">
                                        <button type="submit" class="btn sanclear btn-block fw-bold rounded-pill " style="width: 300px; height: 50px;">Iniciar Sesion</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    

    <!-- jQuery, Popper.js, Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
    
    <!-- Script de registro -->

</body>
</html>